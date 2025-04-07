<!--suppress ALL -->
<template>
    <div>
        <section id="Total_order" class="openTicket">
            <div class="container custom_width">
                <div class="row">
                    <!-- item 1-->
                    <div class="col-lg-3">
                        <div class="Total_order_item">
                            <!-- Header Part -->
                            <div class="header_part d_flex d_justify">
                                <h3>
                                    <svg
                                        width="19"
                                        height="19"
                                        viewBox="0 0 19 19"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M5.49553 5.80437C5.79653 5.50327 5.97277 5.09964 5.989 4.67421C6.00524 4.24877 5.86027 3.83288 5.58309 3.50972C5.56016 3.48284 5.54824 3.44829 5.54972 3.413C5.5512 3.3777 5.56596 3.34427 5.59105 3.3194L7.18775 1.72089C7.21415 1.69451 7.24994 1.67969 7.28725 1.67969C7.32457 1.67969 7.36036 1.69451 7.38675 1.72089L9.93179 4.26592C10.0257 4.3598 10.0964 4.47426 10.1384 4.60024C10.1802 4.72651 10.2508 4.8413 10.3447 4.9355C10.4386 5.02971 10.5532 5.10074 10.6793 5.14297C10.8054 5.18497 10.9199 5.2557 11.014 5.34957L17.5408 11.875C17.5672 11.9014 17.582 11.9371 17.582 11.9745C17.582 12.0118 17.5672 12.0476 17.5408 12.074L15.9441 13.6707C15.9193 13.6958 15.8858 13.7105 15.8505 13.712C15.8152 13.7135 15.7807 13.7016 15.7538 13.6786C15.4307 13.4012 15.0147 13.2559 14.5892 13.2721C14.1636 13.2882 13.7598 13.4645 13.4586 13.7657C13.1575 14.0668 12.9812 14.4706 12.9651 14.8962C12.9489 15.3218 13.0941 15.7378 13.3716 16.0608C13.3945 16.0877 13.4064 16.1223 13.405 16.1576C13.4035 16.1929 13.3887 16.2263 13.3636 16.2512L11.7669 17.8479C11.7405 17.8742 11.7047 17.8891 11.6674 17.8891C11.6301 17.8891 11.5943 17.8742 11.5679 17.8479L5.04072 11.321C4.94686 11.227 4.87613 11.1124 4.83412 10.9863C4.79232 10.8601 4.72167 10.7453 4.62778 10.6511C4.53388 10.5569 4.41933 10.4858 4.2932 10.4436C4.16723 10.4017 4.05276 10.3309 3.95888 10.237L1.41385 7.69198C1.38747 7.66559 1.37265 7.6298 1.37265 7.59248C1.37265 7.55516 1.38747 7.51937 1.41385 7.49298L3.01056 5.89627C3.03543 5.87119 3.06886 5.85642 3.10415 5.85495C3.13945 5.85347 3.174 5.86539 3.20087 5.88832C3.52359 6.16596 3.93922 6.31156 4.36465 6.296C4.79008 6.28044 5.19396 6.10486 5.49553 5.80437V5.80437Z"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                        />
                                        <path
                                            d="M9.67632 5.60322L10.2737 5.00586M8.08359 7.19595L8.48196 6.79795M6.49087 8.78903L6.88887 8.39067M4.69878 10.5808L5.29614 9.9834"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    Total Ticket
                                </h3>
                            </div>

                            <!-- Middle Part -->
                            <div class="middle_part">
                                <h2>{{ total }}</h2>
                            </div>

                            <!-- last_part -->
                            <div class="last_part">
                                <div class="last_part">
                                    <ul>
                                        <li><p>Total Ticket Created</p></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- item 2-->
                    <div class="col-lg-3">
                        <div class="Total_order_item">
                            <!-- Header Part -->
                            <div class="header_part d_flex d_justify">
                                <h3>
                                    <svg
                                        width="19"
                                        height="19"
                                        viewBox="0 0 19 19"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M5.49553 5.80437C5.79653 5.50327 5.97277 5.09964 5.989 4.67421C6.00524 4.24877 5.86027 3.83288 5.58309 3.50972C5.56016 3.48284 5.54824 3.44829 5.54972 3.413C5.5512 3.3777 5.56596 3.34427 5.59105 3.3194L7.18775 1.72089C7.21415 1.69451 7.24994 1.67969 7.28725 1.67969C7.32457 1.67969 7.36036 1.69451 7.38675 1.72089L9.93179 4.26592C10.0257 4.3598 10.0964 4.47426 10.1384 4.60024C10.1802 4.72651 10.2508 4.8413 10.3447 4.9355C10.4386 5.02971 10.5532 5.10074 10.6793 5.14297C10.8054 5.18497 10.9199 5.2557 11.014 5.34957L17.5408 11.875C17.5672 11.9014 17.582 11.9371 17.582 11.9745C17.582 12.0118 17.5672 12.0476 17.5408 12.074L15.9441 13.6707C15.9193 13.6958 15.8858 13.7105 15.8505 13.712C15.8152 13.7135 15.7807 13.7016 15.7538 13.6786C15.4307 13.4012 15.0147 13.2559 14.5892 13.2721C14.1636 13.2882 13.7598 13.4645 13.4586 13.7657C13.1575 14.0668 12.9812 14.4706 12.9651 14.8962C12.9489 15.3218 13.0941 15.7378 13.3716 16.0608C13.3945 16.0877 13.4064 16.1223 13.405 16.1576C13.4035 16.1929 13.3887 16.2263 13.3636 16.2512L11.7669 17.8479C11.7405 17.8742 11.7047 17.8891 11.6674 17.8891C11.6301 17.8891 11.5943 17.8742 11.5679 17.8479L5.04072 11.321C4.94686 11.227 4.87613 11.1124 4.83412 10.9863C4.79232 10.8601 4.72167 10.7453 4.62778 10.6511C4.53388 10.5569 4.41933 10.4858 4.2932 10.4436C4.16723 10.4017 4.05276 10.3309 3.95888 10.237L1.41385 7.69198C1.38747 7.66559 1.37265 7.6298 1.37265 7.59248C1.37265 7.55516 1.38747 7.51937 1.41385 7.49298L3.01056 5.89627C3.03543 5.87119 3.06886 5.85642 3.10415 5.85495C3.13945 5.85347 3.174 5.86539 3.20087 5.88832C3.52359 6.16596 3.93922 6.31156 4.36465 6.296C4.79008 6.28044 5.19396 6.10486 5.49553 5.80437V5.80437Z"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                        />
                                        <path
                                            d="M9.67632 5.60322L10.2737 5.00586M8.08359 7.19595L8.48196 6.79795M6.49087 8.78903L6.88887 8.39067M4.69878 10.5808L5.29614 9.9834"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    Open Ticket
                                </h3>
                            </div>

                            <!-- Middle Part -->
                            <div class="middle_part">
                                <h2>{{ open }}</h2>
                            </div>

                            <!-- last_part -->
                            <div class="last_part">
                                <ul>
                                    <li><p>Ticket Process Increased</p></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- item 3-->
                    <div class="col-lg-3">
                        <div class="Total_order_item">
                            <!-- Header Part -->
                            <div class="header_part d_flex d_justify">
                                <h3>
                                    <svg
                                        width="19"
                                        height="19"
                                        viewBox="0 0 19 19"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M5.49553 5.80437C5.79653 5.50327 5.97277 5.09964 5.989 4.67421C6.00524 4.24877 5.86027 3.83288 5.58309 3.50972C5.56016 3.48284 5.54824 3.44829 5.54972 3.413C5.5512 3.3777 5.56596 3.34427 5.59105 3.3194L7.18775 1.72089C7.21415 1.69451 7.24994 1.67969 7.28725 1.67969C7.32457 1.67969 7.36036 1.69451 7.38675 1.72089L9.93179 4.26592C10.0257 4.3598 10.0964 4.47426 10.1384 4.60024C10.1802 4.72651 10.2508 4.8413 10.3447 4.9355C10.4386 5.02971 10.5532 5.10074 10.6793 5.14297C10.8054 5.18497 10.9199 5.2557 11.014 5.34957L17.5408 11.875C17.5672 11.9014 17.582 11.9371 17.582 11.9745C17.582 12.0118 17.5672 12.0476 17.5408 12.074L15.9441 13.6707C15.9193 13.6958 15.8858 13.7105 15.8505 13.712C15.8152 13.7135 15.7807 13.7016 15.7538 13.6786C15.4307 13.4012 15.0147 13.2559 14.5892 13.2721C14.1636 13.2882 13.7598 13.4645 13.4586 13.7657C13.1575 14.0668 12.9812 14.4706 12.9651 14.8962C12.9489 15.3218 13.0941 15.7378 13.3716 16.0608C13.3945 16.0877 13.4064 16.1223 13.405 16.1576C13.4035 16.1929 13.3887 16.2263 13.3636 16.2512L11.7669 17.8479C11.7405 17.8742 11.7047 17.8891 11.6674 17.8891C11.6301 17.8891 11.5943 17.8742 11.5679 17.8479L5.04072 11.321C4.94686 11.227 4.87613 11.1124 4.83412 10.9863C4.79232 10.8601 4.72167 10.7453 4.62778 10.6511C4.53388 10.5569 4.41933 10.4858 4.2932 10.4436C4.16723 10.4017 4.05276 10.3309 3.95888 10.237L1.41385 7.69198C1.38747 7.66559 1.37265 7.6298 1.37265 7.59248C1.37265 7.55516 1.38747 7.51937 1.41385 7.49298L3.01056 5.89627C3.03543 5.87119 3.06886 5.85642 3.10415 5.85495C3.13945 5.85347 3.174 5.86539 3.20087 5.88832C3.52359 6.16596 3.93922 6.31156 4.36465 6.296C4.79008 6.28044 5.19396 6.10486 5.49553 5.80437V5.80437Z"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                        />
                                        <path
                                            d="M9.67632 5.60322L10.2737 5.00586M8.08359 7.19595L8.48196 6.79795M6.49087 8.78903L6.88887 8.39067M4.69878 10.5808L5.29614 9.9834"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    Processing Ticket
                                </h3>
                            </div>

                            <!-- Middle Part -->
                            <div class="middle_part">
                                <h2>{{ processing }}</h2>
                            </div>

                            <!-- last_part -->
                            <div class="last_part">
                                <ul>
                                    <li><p>Ticket Solving Is On Process</p></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- item 4-->
                    <div class="col-lg-3">
                        <div class="Total_order_item">
                            <!-- Header Part -->
                            <div class="header_part d_flex d_justify">
                                <h3>
                                    <svg
                                        width="19"
                                        height="19"
                                        viewBox="0 0 19 19"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M5.49553 5.80437C5.79653 5.50327 5.97277 5.09964 5.989 4.67421C6.00524 4.24877 5.86027 3.83288 5.58309 3.50972C5.56016 3.48284 5.54824 3.44829 5.54972 3.413C5.5512 3.3777 5.56596 3.34427 5.59105 3.3194L7.18775 1.72089C7.21415 1.69451 7.24994 1.67969 7.28725 1.67969C7.32457 1.67969 7.36036 1.69451 7.38675 1.72089L9.93179 4.26592C10.0257 4.3598 10.0964 4.47426 10.1384 4.60024C10.1802 4.72651 10.2508 4.8413 10.3447 4.9355C10.4386 5.02971 10.5532 5.10074 10.6793 5.14297C10.8054 5.18497 10.9199 5.2557 11.014 5.34957L17.5408 11.875C17.5672 11.9014 17.582 11.9371 17.582 11.9745C17.582 12.0118 17.5672 12.0476 17.5408 12.074L15.9441 13.6707C15.9193 13.6958 15.8858 13.7105 15.8505 13.712C15.8152 13.7135 15.7807 13.7016 15.7538 13.6786C15.4307 13.4012 15.0147 13.2559 14.5892 13.2721C14.1636 13.2882 13.7598 13.4645 13.4586 13.7657C13.1575 14.0668 12.9812 14.4706 12.9651 14.8962C12.9489 15.3218 13.0941 15.7378 13.3716 16.0608C13.3945 16.0877 13.4064 16.1223 13.405 16.1576C13.4035 16.1929 13.3887 16.2263 13.3636 16.2512L11.7669 17.8479C11.7405 17.8742 11.7047 17.8891 11.6674 17.8891C11.6301 17.8891 11.5943 17.8742 11.5679 17.8479L5.04072 11.321C4.94686 11.227 4.87613 11.1124 4.83412 10.9863C4.79232 10.8601 4.72167 10.7453 4.62778 10.6511C4.53388 10.5569 4.41933 10.4858 4.2932 10.4436C4.16723 10.4017 4.05276 10.3309 3.95888 10.237L1.41385 7.69198C1.38747 7.66559 1.37265 7.6298 1.37265 7.59248C1.37265 7.55516 1.38747 7.51937 1.41385 7.49298L3.01056 5.89627C3.03543 5.87119 3.06886 5.85642 3.10415 5.85495C3.13945 5.85347 3.174 5.86539 3.20087 5.88832C3.52359 6.16596 3.93922 6.31156 4.36465 6.296C4.79008 6.28044 5.19396 6.10486 5.49553 5.80437V5.80437Z"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                        />
                                        <path
                                            d="M9.67632 5.60322L10.2737 5.00586M8.08359 7.19595L8.48196 6.79795M6.49087 8.78903L6.88887 8.39067M4.69878 10.5808L5.29614 9.9834"
                                            stroke="#747474"
                                            stroke-width="1.56863"
                                            stroke-miterlimit="10"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                    Solved Ticket
                                </h3>
                            </div>

                            <!-- Middle Part -->
                            <div class="middle_part">
                                <h2>{{ solved }}</h2>
                            </div>

                            <!-- last_part -->
                            <div class="last_part">
                                <ul>
                                    <li><p>Ticket Solved And Closed</p></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="section_gaps"></div>

        <support-ticket-list
            @add="[(showModal = true), (errors = {})]"
            :tickets="tickets"
            @supportTickets="countOpenUpdate"
        />

        <div class="section_gaps"></div>
        <form
            class="mt-4"
            @submit.prevent="handleSubmit"
            enctype="multipart/form-data"
        >
            <Modal :show="showModal" @close="showModal = false">
                <template #header>
                    <h6>Add Support ticket</h6>
                </template>

                <template #default>
                    <div class="form-group mb-2 mt-4">
                        <label for="merchants">Merchant *</label>
                        <select
                            class="form-control"
                            v-model="form.user_id"
                            @change="validate('user_id')"
                        >
                            <option disabled value="">Please select one</option>
                            <option
                                :value="item.id"
                                v-for="item in merchant_list"
                                :key="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <span
                            class="validation-error-message"
                            v-if="!!errors.user_id"
                            >{{ errors.user_id }}</span
                        >
                    </div>

                    <div class="form-group mb-2">
                        <label
                            for="subject"
                            :class="[
                                'mb-0',
                                !!errors.subject && 'validation-error-label',
                            ]"
                            >Subject *</label
                        >
                        <input
                            type="text"
                            v-model="form.subject"
                            name="subject"
                            :class="[
                                'form-control',
                                !!errors.subject && 'validation-error',
                            ]"
                            @blur="validate('subject')"
                            @keypress="validate('subject')"
                        />
                        <span
                            class="validation-error-message"
                            v-if="!!errors.subject"
                            >{{ errors.subject }}</span
                        >
                    </div>

                    <div class="form-group mb-2">
                        <label
                            for="description"
                            :class="[
                                'mb-0',
                                !!errors.content && 'validation-error-label',
                            ]"
                            >Description *</label
                        >
                        <textarea
                            type="text"
                            v-model="form.content"
                            :class="[
                                'form-control',
                                !!errors.content && 'validation-error',
                            ]"
                            rows="5"
                            @blur="validate('content')"
                            @keypress="validate('content')"
                        ></textarea>
                        <span
                            class="validation-error-message"
                            v-if="!!errors.content"
                            >{{ errors.content }}</span
                        >
                    </div>

                    <div class="form-group mb-2">
                        <label for="">Attachments *</label>
                        <input
                            type="file"
                            name="attachment"
                            class="file-control"
                            @change="fileUpload"
                        />
                        <span
                            class="validation-error-message"
                            v-if="!!errors.attachment"
                            >{{ errors.attachment }}</span
                        >
                    </div>
                </template>
            </Modal>
        </form>
    </div>
</template>

<script>
import Modal from "./Modal.vue";
import SupportTicketList from "./SupportTicketList.vue";
import * as yup from "yup";

const ticketsSchema = yup.object().shape({
    subject: yup.string().required().min(8),
    content: yup.string().required().min(20),
    attachment: yup
        .mixed()
        .test(
            "type",
            "Supported file types Image and PDF only",
            function (value) {
                if (value === "undefined" || value !== null) {
                    return (
                        value &&
                        (value.type === "image/jpg" ||
                            value.type === "image/jpeg" ||
                            value.type === "image/png" ||
                            value.type === "application/pdf")
                    );
                } else {
                    return true;
                }
            }
        ),
    user_id: yup.string().required("Please Select Merchant"),
});

export default {
    props: {
        merchant_list: Array,
    },
    components: {
        SupportTicketList,
        Modal,
    },
    data() {
        return {
            showModal: false,
            form: {
                user_id: "",
                subject: "",
                content: "",
                attachment: null,
            },
            errors: {
                user_id: "",
                subject: "",
                content: "",
                attachment: "",
            },
            tickets: [],
            total: 0,
            open: 0,
            processing: 0,
            solved: 0,
        };
    },
    methods: {
        validate(field) {
            ticketsSchema
                .validateAt(field, this.form)
                .then(() => {
                    this.errors[field] = "";
                })
                .catch((err) => {
                    this.errors[field] = err.message;
                });
        },
        handleSubmit() {
            ticketsSchema
                .validate(this.form, { abortEarly: false })
                .then(() => {
                    this.errors = {};

                    const formData = new FormData();
                    formData.append("user_id", this.form.user_id);
                    formData.append("subject", this.form.subject);
                    formData.append("content", this.form.content);
                    formData.append("attachment", this.form.attachment);

                    axios
                        .post("/panel/support-ticket/store", formData, {
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                        })
                        .then((response) => {
                            this.showModal = false;
                            this.form.subject = "";
                            this.form.content = "";
                            this.form.attachment = null;
                            this.form.user_id = null;

                            this.fetchTickets();
                        })
                        .catch((error) => {
                            console.log("validation", error);
                        });
                })
                .catch((err) => {
                    err.inner.forEach((error) => {
                        this.errors[error.path] = error.message;
                    });
                });
        },
        fileUpload(event) {
            this.form.attachment = event.target["files"][0];
            this.validate("attachment");
        },
        fetchTickets() {
            axios.get("/panel/support-ticket/tickets").then((response) => {
                this.tickets = response.data.data.tickets.data;
                this.total = response.data.data.total;
                this.open = response.data.data["counts"]["opened"]
                    ? response.data.data["counts"]["opened"]
                    : 0;
                this.processing = response.data.data["counts"]["processing"]
                    ? response.data.data["counts"]["processing"]
                    : 0;
                this.solved = response.data.data["counts"]["solved"]
                    ? response.data.data["counts"]["solved"]
                    : 0;
            });
        },
        countOpenUpdate() {
            this.fetchTickets();
        },
    },
    mounted() {
        this.fetchTickets();
    },
};
</script>

<style>
.file-control::file-selector-button {
    background: #fff;
    color: #000;
    border: 0px;
    border-right: 1px solid #e5e5e5;
    padding: 0px 15px;
    margin-right: 20px;
    transition: 0.5s;
}
</style>
