<!--suppress ALL -->
<template>
    <section id="ClientList" class="openTicket">
        <div class="container custom_width">
            <!-- Header -->
            <div class="row d_flex">
                <div class="col-lg-3">
                    <div class="header_part d_flex">
                        <!-- svg -->
                        <div class="svg">
                            <svg
                                width="44"
                                height="44"
                                viewBox="0 0 44 44"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M11.6828 11.6874C12.4627 10.9073 12.9193 9.86142 12.9614 8.75907C13.0035 7.65672 12.6278 6.57912 11.9096 5.74178C11.8502 5.67214 11.8193 5.58262 11.8232 5.49117C11.827 5.39972 11.8652 5.31309 11.9303 5.24865L16.0675 1.10675C16.1359 1.0384 16.2286 1 16.3253 1C16.422 1 16.5147 1.0384 16.5831 1.10675L23.1775 7.70117C23.4208 7.94441 23.6041 8.24101 23.7128 8.56742C23.8211 8.89459 24.0042 9.19202 24.2475 9.43612C24.4908 9.68022 24.7876 9.86427 25.1144 9.97368C25.441 10.0825 25.7379 10.2658 25.9816 10.509L42.8932 27.4169C42.9616 27.4853 43 27.578 43 27.6747C43 27.7714 42.9616 27.8641 42.8932 27.9325L38.756 32.0697C38.6916 32.1348 38.605 32.173 38.5135 32.1768C38.4221 32.1807 38.3325 32.1498 38.2629 32.0904C37.4257 31.3714 36.3479 30.9952 35.2452 31.037C34.1424 31.0788 33.0962 31.5356 32.3159 32.3159C31.5356 33.0962 31.0788 34.1424 31.037 35.2452C30.9952 36.3479 31.3714 37.4257 32.0904 38.2629C32.1498 38.3325 32.1807 38.4221 32.1768 38.5135C32.173 38.605 32.1348 38.6916 32.0697 38.756L27.9325 42.8932C27.8641 42.9616 27.7714 43 27.6747 43C27.578 43 27.4853 42.9616 27.4169 42.8932L10.5043 25.9816C10.2611 25.7379 10.0778 25.441 9.96899 25.1144C9.86068 24.7872 9.67762 24.4898 9.43433 24.2457C9.19105 24.0016 8.89423 23.8175 8.56742 23.7081C8.24101 23.5994 7.94441 23.4161 7.70117 23.1728L1.10676 16.5784C1.0384 16.51 1 16.4173 1 16.3206C1 16.2239 1.0384 16.1312 1.10676 16.0628L5.24397 11.9256C5.3084 11.8606 5.39503 11.8223 5.48648 11.8185C5.57794 11.8147 5.66745 11.8455 5.73709 11.9049C6.57328 12.6244 7.65021 13.0016 8.75254 12.9613C9.85488 12.921 10.9014 12.466 11.6828 11.6874V11.6874Z"
                                    stroke="#747474"
                                    stroke-width="2"
                                    stroke-miterlimit="10"
                                />
                                <path
                                    d="M22.5161 11.1662L24.064 9.61841M18.3892 15.2931L19.4214 14.2619M14.2623 19.421L15.2936 18.3888M9.61888 24.0635L11.1667 22.5157"
                                    stroke="#747474"
                                    stroke-width="2"
                                    stroke-miterlimit="10"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>

                        <!-- Text Part -->
                        <div class="text">
                            <h3>All Support Tickets</h3>
                            <p>List of tickets opened by clients</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="FilterBy d_flex justify-content-end">
                        <div class="dropdown_part" style="margin-right: 1rem">
                            <span
                                class="dropdown-toggle d_flex"
                                id="dropdownMenuButton1"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                {{ filter.toUpperCase() }}
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
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="#"
                                        @click.prevent="
                                            supportTicketSearch('today')
                                        "
                                        >Today</a
                                    >
                                </li>
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="#"
                                        @click.prevent="
                                            supportTicketSearch('yesterday')
                                        "
                                        >Yesterday
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="#"
                                        @click.prevent="
                                            supportTicketSearch('weekly')
                                        "
                                        >Weekly
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="#"
                                        @click.prevent="
                                            supportTicketSearch('monthly')
                                        "
                                        >Monthly
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="#"
                                        @click.prevent="
                                            supportTicketSearch('yearly')
                                        "
                                        >Yearly
                                    </a>
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

                        <!-- Filter By Item -->
                        <!-- <div class="FilterBy_item d_flex">
                            <div class="dropdown_part form-control">
                                <span
                                    class="dropdown-toggle d_flex"
                                    id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <p>Select Status</p>
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
                                            ></path>
                                        </svg>
                                    </div>
                                </span>
                                <ul
                                    class="dropdown-menu"
                                    aria-labelledby="dropdownMenuButton1"
                                >
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            >Active</a
                                        >
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            >Blocked</a
                                        >
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            >Inactive</a
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
                                            ></path>
                                        </svg>
                                    </div>
                                </ul>
                            </div>
                        </div> -->

                        <!-- Filter By Item -->
                        <div class="FilterBy_item">
                            <div class="custome_input">
                                <input
                                    type="text"
                                    name=""
                                    class="form-control form-control-lg"
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
                            <a @click="$emit('add')" class="btn btn-default"
                                >Add New</a
                            >
                        </div>
                    </div>
                </div>

                <!-- Table Part  -->
                <div class="col-lg-12">
                    <div class="table_part">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>SL</th>
                                    <th>Client Name</th>
                                    <th>Ticket No.</th>
                                    <th>Subject For Ticket</th>
                                    <th>Submission Time</th>
                                    <th>Assignee</th>
                                    <th>Status</th>
                                </tr>

                                <!-- item -->
                                <tr
                                    v-for="(ticket, index) in tickets"
                                    :key="ticket.id"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td class="name">
                                        {{ ticket["merchant"]["name"] }}
                                    </td>
                                    <td>#{{ ticket["ticket_id"] }}</td>
                                    <td class="subject_for_ticket">
                                        <a
                                            :href="
                                                '/panel/support-ticket/' +
                                                `${ticket.uuid}`
                                            "
                                            >{{ ticket.subject }}</a
                                        >
                                    </td>
                                    <td>{{ ticket.created_at }}</td>
                                    <td>{{ ticket.staff_id }}</td>
                                    <td>
                                        <div class="dropdown_part">
                                            <span
                                                class="dropdown-toggle d_flex"
                                                id="dropdownMenuButton1"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                {{ tickets[index].status }}
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
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="#"
                                                        @click.prevent="
                                                            statusUpdate(
                                                                ticket.id,
                                                                'opened'
                                                            )
                                                        "
                                                        >Opened</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="#"
                                                        @click.prevent="
                                                            statusUpdate(
                                                                ticket.id,
                                                                'processing'
                                                            )
                                                        "
                                                        >Processing
                                                    </a>
                                                </li>
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="#"
                                                        @click.prevent="
                                                            statusUpdate(
                                                                ticket.id,
                                                                'solved'
                                                            )
                                                        "
                                                        >Solved
                                                    </a>
                                                </li>
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="#"
                                                        @click.prevent="
                                                            statusUpdate(
                                                                ticket.id,
                                                                'closed'
                                                            )
                                                        "
                                                        >Closed
                                                    </a>
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
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            tickets: [],
            status: "",
            filter: "Filter",
        };
    },
    methods: {
        fetchTickets() {
            axios.get("/panel/support-ticket/tickets").then((response) => {
                this.tickets = response.data.data.tickets;
            });
        },
        statusUpdate(id, status) {
            const formData = new FormData();
            formData.append("status", status);

            axios
                .post(`/panel/support-ticket/status-update/${id}`, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.fetchTickets();
                    this.$emit("supportTickets", "");
                });
        },
        supportTicketSearch(date) {
            this.filter = date;
            const formData = new FormData();
            formData.append("date", date);

            axios
                .post("/panel/support-ticket/multi-search", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.tickets = response.data.data;
                });
        },
    },
    mounted() {
        this.fetchTickets();
    },
};
</script>
