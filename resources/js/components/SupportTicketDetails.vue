<!--suppress ALL -->
<template>
    <div>
        <section id="open_ticket_details">

            <div class="container custom_width">

                <!-- open_ticket_details -->
                <div class="row">

                    <div class="col-lg-12">

                        <div class="open_ticket_details_content">

                            <h3>Support Ticket No. <span>#{{ ticket.ticket_id }}</span></h3>

                            <!-- Create Ticket -->
                            <div class="create_ticket">

                                <ul>
                                    <li><span>Created On:</span> 22 June,2022</li>
                                    <li class="name"><span>Created By:</span> {{ ticket?.merchant?.name }}</li>
                                    <li><span>Assigned To:</span>
                                        {{ ticket.staff_id ? ticket?.staff?.name : 'No one assigned' }}
                                    </li>
                                    <li class="open"><span>Status:</span> {{
                                            ticket.status && capitalized(ticket.status)
                                        }}
                                    </li>

                                    <li><span>Shop ID:</span> {{ ticket.shop_id }}</li>
                                </ul>

                            </div>

                            <div class="TicketDetail">

                                <h3>Subject</h3>

                                <div class="custome_input">
                                    <textarea rows="1" disabled>{{ ticket?.subject }}</textarea>
                                </div>

                            </div>


                            <!-- Ticket Details -->
                            <div class="TicketDetail">

                                <h3>Ticket Details</h3>

                                <div class="custome_input">
                                    <textarea rows="6" disabled>{{ ticket?.content }}</textarea>
                                </div>


                                <a href="" v-if="ticket.attachment_id">Download Attachment</a>

                            </div>

                            <!-- Reply Of Ticket -->
                            <div class="TicketDetail nobg">

                                <h3 :class="[!!errors.content && 'validation-error-h']">Reply Of Ticket</h3>

                                <div class="custome_input">
                                    <textarea v-model="form.content" :class="[!!errors.content && 'validation-error-textarea' ]" rows="6"  @blur="validate('content')" @keypress="validate('content')"></textarea>
                                </div>
                                <span class="validation-error-message" v-if="!!errors.content">{{ errors.content }}</span>

                            </div>

                            <!-- Add Attachment -->
                            <div class="TicketDetail">

                                <h3>Add Attachment</h3>

                                <div class="custome_input">
                                    <input type="file" @change="fileUpload">
                                    <span class="validation-error-message" v-if="!!errors.attachment">{{ errors.attachment }}</span>
                                </div>


                                <a @click.prevent="replyToTickets" class="Send">Save</a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <div class="section_gaps"></div>

        <section id="ClientList" class="openTicket">

            <div class="container custom_width">

                <!-- Header -->
                <div class="row d_flex">

                    <div class="col-lg-5">

                        <div class="header_part d_flex">

                            <!-- svg -->
                            <div class="svg">
                                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.6828 11.6874C12.4627 10.9073 12.9193 9.86142 12.9614 8.75907C13.0035 7.65672 12.6278 6.57912 11.9096 5.74178C11.8502 5.67214 11.8193 5.58262 11.8232 5.49117C11.827 5.39972 11.8652 5.31309 11.9303 5.24865L16.0675 1.10675C16.1359 1.0384 16.2286 1 16.3253 1C16.422 1 16.5147 1.0384 16.5831 1.10675L23.1775 7.70117C23.4208 7.94441 23.6041 8.24101 23.7128 8.56742C23.8211 8.89459 24.0042 9.19202 24.2475 9.43612C24.4908 9.68022 24.7876 9.86427 25.1144 9.97368C25.441 10.0825 25.7379 10.2658 25.9816 10.509L42.8932 27.4169C42.9616 27.4853 43 27.578 43 27.6747C43 27.7714 42.9616 27.8641 42.8932 27.9325L38.756 32.0697C38.6916 32.1348 38.605 32.173 38.5135 32.1768C38.4221 32.1807 38.3325 32.1498 38.2629 32.0904C37.4257 31.3714 36.3479 30.9952 35.2452 31.037C34.1424 31.0788 33.0962 31.5356 32.3159 32.3159C31.5356 33.0962 31.0788 34.1424 31.037 35.2452C30.9952 36.3479 31.3714 37.4257 32.0904 38.2629C32.1498 38.3325 32.1807 38.4221 32.1768 38.5135C32.173 38.605 32.1348 38.6916 32.0697 38.756L27.9325 42.8932C27.8641 42.9616 27.7714 43 27.6747 43C27.578 43 27.4853 42.9616 27.4169 42.8932L10.5043 25.9816C10.2611 25.7379 10.0778 25.441 9.96899 25.1144C9.86068 24.7872 9.67762 24.4898 9.43433 24.2457C9.19105 24.0016 8.89423 23.8175 8.56742 23.7081C8.24101 23.5994 7.94441 23.4161 7.70117 23.1728L1.10676 16.5784C1.0384 16.51 1 16.4173 1 16.3206C1 16.2239 1.0384 16.1312 1.10676 16.0628L5.24397 11.9256C5.3084 11.8606 5.39503 11.8223 5.48648 11.8185C5.57794 11.8147 5.66745 11.8455 5.73709 11.9049C6.57328 12.6244 7.65021 13.0016 8.75254 12.9613C9.85488 12.921 10.9014 12.466 11.6828 11.6874V11.6874Z" stroke="#747474" stroke-width="2" stroke-miterlimit="10"/>
                                    <path d="M22.5161 11.166L24.064 9.61816M18.3892 15.2929L19.4214 14.2616M14.2623 19.4207L15.2936 18.3885M9.61888 24.0632L11.1667 22.5154" stroke="#747474" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round"/>
                                </svg>

                            </div>

                            <!-- Text Part -->
                            <div class="text">
                                <h3>Ticket Conversation</h3>
                                <p>Conversation of tickets opened by clients</p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>


        <section id="open_ticket_details" class="CustomerName">

            <div class="container custom_width">

                <!-- open_ticket_details -->
                <div class="row">

                    <div class="col-lg-12">

                        <div class="open_ticket_details_content" v-for="comment in ticket.comments">

                            <!-- Ticket Details -->
                            <div class="TicketDetail" :class="comment.user_id !== ticket.user_id ? 'bg_white' : ''">

                                <h3>{{ comment.user.name }}  <span> {{ comment.created_at }}</span></h3>

                                <p>{{ comment?.content }}</p>

                                <a @click="download(comment.attachment_id)" v-if="comment.attachment_id !== null">Download Attachment</a>

                            </div>




                        </div>

                    </div>

                </div>

            </div>

        </section>

        <div class="section_gaps"></div>
    </div>
</template>

<script>

import * as yup from "yup";

const ticketsSchema = yup.object().shape({
    content: yup.string().required().min(6),
    attachment: yup.mixed().test("type", 'Supported file types Image and PDF only', function (value) {
        if (value === 'undefined' || value !== null) {
            return value && (value.type === 'image/jpg' || value.type === 'image/jpeg' || value.type === 'image/png' || value.type === 'application/pdf');
        } else {
            return true;
        }

    })
});
export default {
    name: "SupportTicketDetails",
    props: {
        uuid: String
    },
    data() {
        return {
            ticket: {},
            form: {
                ticket_id: null,
                content: "",
                attachment: null

            },
            errors: {
                content: "",
                attachment: "",
            },
        }
    },
    mounted() {
        this.fetchTicketDetails()

    },
    methods: {
        validate(field) {
            ticketsSchema
                .validateAt(field, this.form)
                .then(() => {
                    this.errors[field] = "";
                })
                .catch(err => {
                    this.errors[field] = err.message;
                })
        },
        capitalized(name) {
            const capitalizedFirst = name[0].toUpperCase();
            const rest = name.slice(1);
            return capitalizedFirst + rest;
        },
        fetchTicketDetails() {
            axios.get(`/panel/support-ticket/details/${this.uuid}`).then(response => {
                this.ticket = response.data.data
                this.form.ticket_id = response.data.data.id

                console.log(response.data)
            })
        },
        replyToTickets() {
            ticketsSchema
                .validate(this.form, {abortEarly: false})
                .then(() => {
                    this.errors = {};

                    const formData = new FormData();
                    formData.append('ticket_id', this.form.ticket_id)
                    formData.append('content', this.form.content)
                    formData.append('attachment', this.form.attachment)

                    axios.post(`/panel/support-ticket/reply/${this.form.ticket_id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        }
                    }).then(response => {

                        this.form.content = ""
                        this.fetchTicketDetails()
                    })
                })
                .catch(err => {
                    err.inner.forEach(error => {
                        this.errors[error.path] = error.message;
                    });
                })
        },
        fileUpload(event) {
            this.form.attachment = event.target["files"][0];
            this.validate('attachment')
        },
        download(id) {
            axios.get(`/panel/download/${id}/attachment`, {responseType: "blob"}).then(response => {

                console.log(response)
                const url = window.URL.createObjectURL(new Blob([response.data], {
                    type: response.data.type
                }));
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", 'file');
                document.body.appendChild(link);
                link.click();
                link.remove();
            })
        }

    },
}
</script>

<style scoped>
.TicketDetail p {
    margin-top: 20px !important;
}
.validation-error-h {
    color: #A16CF8 !important;
}
.validation-error-textarea {
    border: 2px solid #A16CF8 !important;
}
</style>
