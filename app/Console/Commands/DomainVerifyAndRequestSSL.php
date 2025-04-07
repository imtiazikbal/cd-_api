<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DomainVerifyAndRequestSSL extends Command
{
    protected $signature = 'domain-ssl:request-and-verify';
    protected $description = 'Verify custom domains and request SSL certificates';

    public function handle()
    {
        $expectedCNAME = 'secure.funnelliner.com';
        $expectedARecord = '47.128.198.63';

        $today = Carbon::today()->toDateString();

        $domains = DB::table('shops')
            ->where('domain_status', '!=', 'connected')
            ->where('domain_status', '!=', 'rejected')
            ->where('ssl_status', '!=', 'connected')
            ->whereDate('domain_request_date', '>=', $today)
            ->get(['id', 'domain_request']);

        $updateData = [];

        foreach ($domains as $domain) {
            if ($this->verifyCNAME($domain->domain_request, $expectedCNAME, $expectedARecord)) {
                $response = $this->requestSSL($domain->domain_request);

                if ($this->checkSSL($domain->domain_request)) {
                    $sslStatus = 'connected';
                    $domainStatus = 'connected';
                } else {
                    $sslStatus = 'failed';
                    $domainStatus = 'failed';
                    $this->deleteSSL($domain->domain_request);
                    Log::error("SSL request failed for domain: {$domain->domain_request}", ['response' => $response->body()]);
                }

                $updateData[] = [
                    'id' => $domain->id,
                    'ssl_status' => $sslStatus,
                    'domain_status' => $domainStatus,
                    'updated_at' => now()
                ];

            }
        }

        if (!empty($updateData)) {
            $this->batchUpdate('shops', $updateData);
        }
    }

    private function verifyCNAME($domain, $expectedCNAME, $expectedARecord)
    {
        try {
            $cnameRecords = dns_get_record($domain, DNS_CNAME);
            foreach ($cnameRecords as $record) {
                if (isset($record['target']) && rtrim($record['target'], '.') === $expectedCNAME) {
                    return true;
                }
            }

            $aRecords = dns_get_record($domain, DNS_A);
            foreach ($aRecords as $record) {
                if (isset($record['ip']) && $record['ip'] === $expectedARecord) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error("DNS lookup failed for domain: {$domain}", ['error' => $e->getMessage()]);
        }

        return false;
    }

    private function requestSSL($domain)
    {
        try {
            return Http::post('https://ssl.funnelliner.com/request-ssl', ['domain' => $domain]);
        } catch (\Exception $e) {
            Log::error("HTTP request failed for SSL certificate: {$domain}", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'HTTP request failed'], 500);
        }
    }

    private function deleteSSL($domain)
    {
        try {
            return Http::post('https://ssl.funnelliner.com/request-ssl-delete', ['domain' => $domain]);
        } catch (\Exception $e) {
            Log::error("HTTP request failed for Delete SSL certificate: {$domain}", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'HTTP request failed'], 500);
        }
    }
    private function checkSSL($domain)
    {
        try {
            $url = "https://{$domain}";
            $response = Http::get($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("SSL check failed for domain: {$domain}", ['error' => $e->getMessage()]);
            return false;
        }
    }
    private function batchUpdate($table, $data)
    {
        $casesSsl = [];
        $casesDomain = [];
        $ids = [];

        foreach ($data as $item) {
            $id = (int) $item['id'];
            $ssl_status = DB::getPdo()->quote($item['ssl_status']);
            $domain_status = DB::getPdo()->quote($item['domain_status']);

            $casesSsl[] = "WHEN id = {$id} THEN {$ssl_status}";
            $casesDomain[] = "WHEN id = {$id} THEN {$domain_status}";

            $ids[] = $id;
        }

        $ids = implode(',', $ids);
        $casesSsl = implode(' ', $casesSsl);
        $casesDomain = implode(' ', $casesDomain);

        $query = "UPDATE {$table} 
                  SET ssl_status = CASE {$casesSsl} ELSE ssl_status END, 
                      domain_status = CASE {$casesDomain} ELSE domain_status END 
                  WHERE id IN ({$ids})";

        DB::statement($query);
    }
}
