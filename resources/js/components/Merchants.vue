<!--suppress ALL -->
<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="FilterBy d_flex">
                <div class="FilterBy_item d_flex">
                    <h3 style="width: 70%">Joining Date:</h3>
                    <input
                        type="date"
                        class="form-control"
                        ref="joining_date"
                        name="joining_date"
                        @change="filterByDate"
                    />
                </div>

                <div class="FilterBy_item d_flex">
                    <h3 style="width: 45%">Order Filter:</h3>
                    <!-- <input
                        type="date"
                        class="form-control"
                        ref="joining_date"
                        name="joining_date"
                        @change="filterByDate"
                    /> -->
                    <VueDatePicker
                        v-model="date"
                        range
                        @update:model-value="handleOrderCount"
                    >
                    </VueDatePicker>
                </div>

                <div class="FilterBy_item d_flex">
                    <h3 style="width: 80%">Status:</h3>
                    <div class="dropdown_part form-control">
                        <span
                            class="dropdown-toggle d_flex"
                            id="dropdownMenuButton1"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <p
                                v-text="
                                    !statusText ? 'Select Status' : statusText
                                "
                            ></p>
                            <div class="arrow">
                                <svg
                                    width="11"
                                    height="6"
                                    viewBox="0 0 11 6"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M0.244629 0.501221L5.40989 5.66649L10.5752 0.501221H0.244629Z"
                                        fill="#747474"
                                    />
                                </svg>
                            </div>
                        </span>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="dropdownMenuButton1"
                        >
                            <li
                                v-for="status in statusList"
                                @click="filterMerchants(status)"
                            >
                                <a class="dropdown-item" href="#">{{
                                    capitalized(status)
                                }}</a>
                            </li>

                            <!-- up arrow -->
                            <div class="up_arrow">
                                <svg
                                    width="11"
                                    height="6"
                                    viewBox="0 0 11 6"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M10.3306 5.16528L5.1653 1.6953e-05L3.48091e-05 5.16528L10.3306 5.16528Z"
                                        fill="#F3ECFF"
                                    />
                                </svg>
                            </div>
                        </ul>
                    </div>
                </div>

                <div class="FilterBy_item">
                    <div class="custome_input">
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            ref="search"
                            @keyup.enter="searchMerchant"
                            placeholder="Search here..."
                        />

                        <div class="search">
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M16.3078 16.7113L12.6943 13.0914M14.6968 8.25366C14.6968 10.0695 13.9754 11.811 12.6914 13.095C11.4074 14.379 9.66595 15.1003 7.8501 15.1003C6.03425 15.1003 4.29277 14.379 3.00876 13.095C1.72476 11.811 1.00342 10.0695 1.00342 8.25366C1.00342 6.43781 1.72476 4.69633 3.00876 3.41233C4.29277 2.12833 6.03425 1.40698 7.8501 1.40698C9.66595 1.40698 11.4074 2.12833 12.6914 3.41233C13.9754 4.69633 14.6968 6.43781 14.6968 8.25366V8.25366Z"
                                    stroke="#A3A3A3"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="FilterBy_item">
                    <div class="clear-filter">
                        <button
                            class="btn btn-default mt-0"
                            @click="clearFilter"
                        >
                            Clear Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="table_part">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>SL</th>
                            <th>Shop Id</th>
                            <th>Company Name</th>
                            <th>Client Name</th>
                            <th>Contact No.</th>
                            <th>Orders</th>
                            <th>Joining Date</th>
                            <th>Next Due Date</th>
                            <th>Set Date</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <!-- <th>Action</th> -->
                        </tr>

                        <tr
                            v-for="(merchant, key) in merchants.data"
                            :key="merchant.id"
                            v-if="merchants?.data?.length > 0"
                        >
                            <td>
                                {{ key + 1 + currentPage * perPage - perPage }}
                            </td>
                            <td>
                                {{
                                    merchant?.shop
                                        ? merchant?.shop?.shop_id
                                        : "None"
                                }}
                            </td>
                            <td class="companyName">
                                {{
                                    merchant?.shop
                                        ? merchant?.shop?.name
                                        : "None"
                                }}
                            </td>
                            <td class="name">
                                <a
                                    :href="
                                        '/panel/merchants/' + `${merchant.id}`
                                    "
                                    >{{ capitalized(merchant.name) }}</a
                                >
                            </td>
                            <td>{{ merchant.phone }}</td>
                            <td>{{ merchant.order_count }}</td>
                            <td>{{ merchant.created_at }}</td>
                            <td class="text-center">
                                {{ merchant.next_due_date }}
                            </td>

                            <td class="text-center">
                                <input
                                    v-model="editedDueDates[merchant.id]"
                                    type="date"
                                    style="width: 79% !important"
                                />
                                <button
                                    @click="
                                        updateDueDate(
                                            merchant.id,
                                            editedDueDates[merchant.id]
                                        )
                                    "
                                    style="
                                        padding: 8px 10px !important;
                                        margin-top: 0px !important;
                                    "
                                >
                                    <i class="fa fa-check"></i>
                                </button>
                            </td>

                            <!--<td class="text-center">
                            <input v-model="editedDueDate" type="date">
                            <button @click="updateDueDate(merchant.id, editedDueDate)">OK</button>
                        </td>-->
                            <!--<td class="text-center">
                            <span class="badge text-2xl"
                                  :class="`${merchant.payment_status === 'unpaid' ? 'bg-danger' : 'bg-success'}`">{{
                                    capitalized(merchant.payment_status)
                                }}</span></td>-->

                            <td>
                                <div class="dropdown_part">
                                    <span
                                        class="dropdown-toggle d_flex"
                                        id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        {{
                                            capitalized(merchant.payment_status)
                                        }}
                                        <div class="arrow">
                                            <svg
                                                width="11"
                                                height="6"
                                                viewBox="0 0 11 6"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M0.244629 0.501221L5.40989 5.66649L10.5752 0.501221H0.244629Z"
                                                    fill="#747474"
                                                />
                                            </svg>
                                        </div>
                                    </span>

                                    <ul
                                        class="dropdown-menu"
                                        aria-labelledby="dropdownMenuButton1"
                                    >
                                        <li
                                            v-for="payment_status in PaymentStatusList"
                                            @click="
                                                updatePaymentStatus(
                                                    merchant.id,
                                                    payment_status
                                                )
                                            "
                                        >
                                            <a
                                                class="dropdown-item"
                                                id="payment_status_change"
                                                >{{
                                                    capitalized(payment_status)
                                                }}</a
                                            >
                                        </li>

                                        <div class="up_arrow">
                                            <svg
                                                width="11"
                                                height="6"
                                                viewBox="0 0 11 6"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M10.3306 5.16528L5.1653 1.6953e-05L3.48091e-05 5.16528L10.3306 5.16528Z"
                                                    fill="#F3ECFF"
                                                />
                                            </svg>
                                        </div>
                                    </ul>
                                </div>
                            </td>

                            <td>
                                <div class="dropdown_part">
                                    <span
                                        class="dropdown-toggle d_flex"
                                        id="dropdownMenuButton1"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        {{ capitalized(merchant.status) }}
                                        <div class="arrow">
                                            <svg
                                                width="11"
                                                height="6"
                                                viewBox="0 0 11 6"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M0.244629 0.501221L5.40989 5.66649L10.5752 0.501221H0.244629Z"
                                                    fill="#747474"
                                                />
                                            </svg>
                                        </div>
                                    </span>

                                    <ul
                                        class="dropdown-menu"
                                        aria-labelledby="dropdownMenuButton1"
                                    >
                                        <li
                                            v-for="status in statusList"
                                            @click="
                                                updateStatus(
                                                    merchant.id,
                                                    status
                                                )
                                            "
                                        >
                                            <a
                                                class="dropdown-item"
                                                id="change-status"
                                                >{{ capitalized(status) }}</a
                                            >
                                        </li>

                                        <div class="up_arrow">
                                            <svg
                                                width="11"
                                                height="6"
                                                viewBox="0 0 11 6"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M10.3306 5.16528L5.1653 1.6953e-05L3.48091e-05 5.16528L10.3306 5.16528Z"
                                                    fill="#F3ECFF"
                                                />
                                            </svg>
                                        </div>
                                    </ul>
                                </div>
                            </td>

                            <!-- Merchant Delete Button -->
                            <!-- <td>
                                <a
                                    @click="deleteMerchant(merchant.id)"
                                    href="javascript:;"
                                    ><i class="fa fa-trash-alt"></i
                                ></a>
                            </td> -->
                        </tr>
                        <tr v-else>
                            <td colspan="9" style="text-align: center">
                                No users Found!
                            </td>
                        </tr>
                    </tbody>
                </table>

                <Paginate
                    :currentPage="currentPage"
                    :totalPage="totalPage"
                    :nextPageUrl="nextPageUrl"
                    :perPage="perPage"
                    :merchants="merchants"
                    :isOrderCount="isOrderCount"
                    :pageNumber="pageNumber"
                    @previous-button-emit="handlePrevPage"
                    @next-button-emit="handleNextPage"
                    @current-page-number="handleSetCurrentPage"
                />
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Paginate from "./Paginate.vue";

export default {
    components: {
        Paginate,
    },
    data() {
        return {
            pageNumber: 10,
            merchants: [],
            statusList: [],
            PaymentStatusList: [],
            currentPage: 1,
            totalPage: 0,
            statusText: "",
            paymentStatusText: "",
            nextPageUrl: null,
            perPage: 0,
            search: "",
            editedDueDates: {},
            date: "",
            isOrderCount: false,
            startDate: "",
            endDate: "",
            joining_date: "",
        };
    },

    mounted() {
        this.fetchMerchants();
        this.fetchStatues();
        this.fetchPaymentStatus();
    },
    methods: {
        handlePrevPage(handlePrevPage) {
            this.prevPage(handlePrevPage);
        },
        handleNextPage(handleNextPage) {
            this.nextPage(handleNextPage);
        },
        handleSetCurrentPage(pageNumber) {
            this.setCurrentPage(pageNumber);
        },
        capitalized(name) {
            // console.log(name.charAt(0).toUpperCase());
            const capitalizedFirst = name[0];
            const rest = name.slice(1);
            return capitalizedFirst + rest;
        },
        searchMerchant(e) {
            const search = e.target.value;
            this.search = search;
            this.fetchMerchants(0, "", search);
        },
        filterByDate(e) {
            this.joining_date = e.target.value;
            const joining_date = e.target.value;
            this.fetchMerchants(0, "", "", joining_date);
        },
        fetchMerchants(
            page = 0,
            status = "",
            search = "",
            joining_date = "",
            limit
        ) {
            axios
                .get("/panel/merchants/merchants", {
                    params: {
                        page,
                        status,
                        search,
                        joining_date,
                        limit,
                    },
                })
                .then((response) => {
                    this.merchants = response.data;
                    this.currentPage = response.data.meta["current_page"];
                    this.totalPage = response.data.meta["total"];
                    this.perPage = response.data.meta["per_page"];
                });
        },
        fetchStatues() {
            axios.get("/panel/merchants/statuses").then((response) => {
                this.statusList = response.data;
            });
        },
        filterMerchants(status) {
            this.statusText = status;
            this.fetchMerchants(this.currentPage, status);
        },
        updateStatus(merchant, status) {
            axios
                .post("/panel/merchants/" + merchant + "/update-status", {
                    status,
                })
                .then((response) => {
                    this.fetchMerchants();
                });
        },

        fetchPaymentStatus() {
            axios.get("/panel/merchants/payment_status").then((response) => {
                this.PaymentStatusList = response.data;
            });
        },
        updatePaymentStatus(merchant, payment_status) {
            axios
                .post(
                    "/panel/merchants/" + merchant + "/update-payment-status",
                    { payment_status }
                )
                .then((response) => {
                    this.fetchMerchants();
                });
        },
        deleteMerchant(merchant) {
            axios
                .post("/panel/merchants/" + merchant + "/delete")
                .then((response) => {
                    this.fetchMerchants();
                });
        },
        clearFilter() {
            this.statusText = "";
            this.$refs["search"].value = "";
            this.$refs["joining_date"].value = "";
            this.fetchMerchants();
        },
        updateDueDate(merchant, nextDueDate) {
            axios
                .put("/panel/merchants/" + merchant + "/updateduedate", {
                    next_due_date: nextDueDate,
                })
                .then((response) => {
                    this.fetchMerchants();
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        setCurrentPage(page) {
            this.currentPage = page;
            if (!this.isOrderCount) {
                this.fetchMerchants(page, "", this.search, this.joining_date);
            } else {
                this.handleOrderCount();
            }
        },
        nextPage(url) {
            if (!this.isOrderCount) {
                if (this.currentPage < this.totalPage) {
                    axios
                        .get(
                            url +
                                `&search=${this.search}&joining_date=${this.joining_date}`
                        )
                        .then((response) => {
                            console.log(response);
                            this.merchants = response.data;
                            this.currentPage =
                                response.data.meta["current_page"];
                        });
                }
            } else {
                if (this.currentPage < this.totalPage) {
                    axios
                        .get(
                            url +
                                `&startDate=${this.startDate}&endDate=${this.endDate}`
                        )
                        .then((response) => {
                            this.merchants = response.data.data;
                            this.currentPage =
                                response.data.meta["current_page"];
                        });
                }
            }
        },
        prevPage(url) {
            if (!this.isOrderCount) {
                if (this.currentPage !== 1) {
                    axios
                        .get(
                            url +
                                `&search=${this.search}&joining_date=${this.joining_date}`
                        )
                        .then((response) => {
                            this.merchants = response.data;
                            this.currentPage =
                                response.data.meta["current_page"];
                        });
                }
            } else {
                if (this.currentPage !== 1) {
                    axios
                        .get(
                            url +
                                `&startDate=${this.startDate}&endDate=${this.endDate}`
                        )
                        .then((response) => {
                            this.merchants = response.data.data;
                            this.currentPage =
                                response.data.meta["current_page"];
                        });
                }
            }
        },
        handleOrderCount() {
            let startDate =
                this.date[0].getFullYear() +
                "-" +
                (this.date[0].getMonth() + 1) +
                "-" +
                this.date[0].getDate();

            let endDate =
                this.date[1].getFullYear() +
                "-" +
                (this.date[1].getMonth() + 1) +
                "-" +
                this.date[1].getDate();

            this.startDate = startDate;
            this.endDate = endDate;
            let pageNumber = this.currentPage;

            axios
                .get(
                    `/panel/merchants/search/order-count?page=${pageNumber}&startDate=${startDate}&endDate=${endDate}`
                )
                .then((response) => {
                    this.isOrderCount = true;
                    this.merchants = response.data.data;
                    this.currentPage = response.data.meta["current_page"];
                    this.totalPage = response.data.meta["total"];
                    this.perPage = response.data.meta["per_page"];
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>

<style>
.page-item {
    cursor: pointer;
}

.page-link,
.page-link:hover {
    color: #a071f1;
}

.page-item.active .page-link {
    background-color: #a071f1 !important;
    border-color: hsl(262deg 82% 69%) !important;
}

.dropdown-menu li:hover {
    cursor: pointer;
}
</style>
