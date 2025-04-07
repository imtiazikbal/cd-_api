import SupportTicketDetails from "./components/SupportTicketDetails";
import "./bootstrap";

window.axios = require("axios");
import { createApp } from "vue";

import Merchants from "./components/Merchants.vue";
import SupportTicket from "./components/SupportTicket.vue";
import SupportTicketList from "./components/SupportTicketList.vue";
import Themes from "./components/Themes.vue";
import Modal from "./components/Modal.vue";
import Staffs from "./components/Staffs.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const app = createApp({});
app.component("merchants", Merchants);
app.component("support-ticket", SupportTicket);
app.component("support-ticket-list", SupportTicketList);
app.component("support-ticket-details", SupportTicketDetails);
app.component("themes", Themes);
app.component("staffs", Staffs);
app.component("modal", Modal);
app.component("VueDatePicker", VueDatePicker);
app.mount("#app");
