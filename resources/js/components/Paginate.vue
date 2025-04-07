<script>
export default {
    props: {
        currentPage: Number,
        totalPage: Number,
        nextPageUrl: String,
        perPage: Number,
        merchants: Object,
        isOrderCount: Boolean,
        pageNumber: Number,
        search: String,
    },
    data() {
        return {};
    },
    methods: {
        emitPrevPage() {
            if (this.isOrderCount) {
                // this is for total order count option
                this.$emit(
                    "previous-button-emit",
                    this.merchants.prev_page_url
                );
            } else {
                this.$emit(
                    "previous-button-emit",
                    this.merchants.meta["prev_page_url"]
                );
            }
        },
        emitNextPage() {
            if (this.isOrderCount) {
                // this is for total order count option
                this.$emit("next-button-emit", this.merchants.next_page_url);
            } else {
                this.$emit(
                    "next-button-emit",
                    this.merchants.meta["next_page_url"]
                );
            }
        },
        emitSetCurrentPage(pageNumber) {
            this.$emit("current-page-number", pageNumber);
        },
    },
};
</script>

<template>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-end">
            <li
                class="page-item"
                :class="{ disabled: currentPage === 1 }"
                @click="emitPrevPage"
            >
                <a class="page-link" tabindex="-1">Previous</a>
            </li>

            <li
                v-for="(pageNumber, i) in totalPage"
                class="page-item"
                :class="{ active: currentPage === pageNumber }"
            >
                <a
                    class="page-link"
                    @click="emitSetCurrentPage(pageNumber)"
                    v-if="
                        i + 1 === 1 ||
                        (currentPage - 3 <= i && currentPage + 3 >= i) ||
                        i === currentPage ||
                        i + 1 === totalPage
                    "
                    :class="{
                        disabled: currentPage === pageNumber,
                        last:
                            pageNumber === totalPage - 1 &&
                            Math.abs(pageNumber - currentPage) > 3,
                        first:
                            pageNumber === 0 &&
                            Math.abs(pageNumber - currentPage) > 3,
                    }"
                >
                    {{ pageNumber }}
                </a>
                <a
                    class="page-link"
                    v-else-if="
                        i === currentPage - (3 + 1) ||
                        i === currentPage + (3 + 1)
                    "
                    >...</a
                >
            </li>

            <li
                class="page-item"
                :class="{ disabled: currentPage === totalPage }"
                @click="emitNextPage"
            >
                <a class="page-link">Next</a>
            </li>
        </ul>
    </nav>
</template>

<style></style>
