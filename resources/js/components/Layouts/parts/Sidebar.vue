<template>
    <!-- Sidebar -->
    <ul
        class="navbar-nav sidebar accordion"
        :class="toggled ? 'toggled' : toogleSidebar"
        id="accordionSidebar"
    >
        <!-- Sidebar - Brand -->
        <!-- <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-laugh-wink text-orange"></i>
            </div>
            <div class="sidebar-brand-text mx-3 text-orange">ARMS Admin</div>
        </a> -->
        <div class="hero d-block mx-auto">
            <div class="hero-img-holder">
                <img
                    :src="[
                        user.profile_picture
                            ? '/uploads/200x200/' + user.profile_picture
                            : '/uploads/joeren.jpg',
                    ]"
                    class="img-fluid"
                    alt=""
                />
            </div>
        </div>
        <h3 class="user-name">{{ user.first_name }} {{ user.last_name }}</h3>
        <hr class="sidebar-divider d-none d-md-block mb-0" />
        <span class="user-role">{{ user.role }}</span>
        <!-- Heading -->

        <div class="sticky-side-menu">
            <!-- <div class="sidebar-heading">
                MENUS
            </div> -->
            <li
                class="nav-item"
                :class="{ active: $route.fullPath === '/app/dashboard' }"
            >
                <router-link class="nav-link" to="/app/dashboard">
                    <i class="bi bi-menu-app"></i>
                    <span>Dashboard</span></router-link
                >
            </li>
            <li
                class="nav-item"
                v-show="show_fileUpload || show_dataEntry"
                :class="[
                    $route.fullPath === '/app/transaction/batch-upload' ||
                    $route.fullPath === '/app/transaction/data-entry'
                        ? 'active'
                        : '',
                ]"
            >
                <a
                    v-show="this.user.role == 'maker'"
                    class="nav-link collapsed"
                    href="#"
                    data-toggle="collapse"
                    data-target="#createTransaction"
                    aria-expanded="true"
                    aria-controls="createTransaction"
                >
                    <i class="bi bi-clipboard-plus"></i>
                    <span>Create Transactions</span>
                </a>
                <div
                    id="createTransaction"
                    class="collapse"
                    aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar"
                >
                    <div class="bg-white py-2 collapse-inner rounded">
                        <router-link
                            v-show="show_fileUpload"
                            to="/app/transaction/batch-upload"
                            class="collapse-item"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/transaction/batch-upload',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/transaction/batch-upload'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Batch Upload
                        </router-link>
                        <router-link
                            v-show="show_dataEntry"
                            to="/app/transaction/data-entry"
                            class="collapse-item"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/transaction/data-entry',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/transaction/data-entry'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Data Entry
                        </router-link>
                    </div>
                </div>
            </li>
            <li
                v-show="show_ammendment"
                class="nav-item item-ammendment"
                :class="{ active: $route.fullPath === '/app/amendment' }"
            >
                <router-link class="nav-link" to="/app/amendment">
                    <i class="bi bi-clock-history"></i>
                    <span>Amendment</span></router-link
                >
            </li>
            <li
                v-show="show_inquiry"
                class="nav-item item-inquiry"
                :class="{ active: $route.fullPath === '/app/inquiry' }"
            >
                <router-link class="nav-link" to="/app/inquiry">
                    <i class="bi bi-question-circle"></i>
                    <span>Inquiry</span></router-link
                >
            </li>
            <li
                class="nav-item"
                v-show="
                    show_monitor || show_pending || show_proccess || show_refund
                "
                :class="[
                    $route.fullPath === '/app/transactions/monitor' ||
                    $route.fullPath === '/app/transactions/pending' ||
                    $route.fullPath === '/app/transactions/proccessed' ||
                    $route.fullPath === '/app/transactions/refund-request'
                        ? 'active'
                        : '',
                ]"
            >
                <a
                    class="nav-link collapsed"
                    href="#"
                    data-toggle="collapse"
                    data-target="#transaction"
                    aria-expanded="true"
                    aria-controls="transaction"
                >
                    <i class="bi bi-journal-bookmark"></i>
                    <span>Transactions</span>
                </a>
                <div
                    id="transaction"
                    class="collapse"
                    aria-labelledby="headingTwo"
                    data-parent="#accordionSidebar"
                >
                    <div class="bg-white py-2 collapse-inner rounded">
                        <router-link
                            v-show="show_monitor"
                            class="collapse-item"
                            to="/app/transactions/monitor"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/transactions/monitor',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/transactions/monitor'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Monitor
                        </router-link>

                        <router-link
                            v-show="show_pending"
                            class="collapse-item"
                            to="/app/transactions/pending"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/transactions/pending',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/transactions/pending'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Pending
                        </router-link>

                        <router-link
                            v-show="show_proccess"
                            class="collapse-item"
                            to="/app/transactions/proccessed"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/transactions/proccessed',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/transactions/proccessed'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Proccessed
                        </router-link>

                        <router-link
                            v-show="show_refund"
                            class="collapse-item"
                            to="/app/transactions/refund-request"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/transactions/refund-request',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/transactions/refund-request'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Refund Request
                        </router-link>
                    </div>
                </div>
            </li>
            <li
                class="nav-item"
                v-show="show_disposition || show_long || show_status"
                :class="[
                    $route.fullPath === '/app/reports/disposition' ||
                    $route.fullPath === '/app/reports/long-outstanding' ||
                    $route.fullPath === '/app/reports/status'
                        ? 'active'
                        : '',
                ]"
            >
                <a
                    class="nav-link collapsed"
                    href="#"
                    data-toggle="collapse"
                    data-target="#report"
                    aria-expanded="true"
                    aria-controls="report"
                >
                    <i class="bi bi-graph-up"></i>
                    <span>Reports</span>
                </a>
                <div
                    id="report"
                    class="collapse"
                    aria-labelledby="headingTwo"
                    data-parent="#accordionSidebar"
                >
                    <div class="bg-white py-2 collapse-inner rounded">
                        <router-link
                            v-show="show_disposition"
                            class="collapse-item"
                            to="/app/reports/disposition"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/reports/disposition',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/reports/disposition'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Disposition
                        </router-link>

                        <router-link
                            v-show="show_long"
                            class="collapse-item"
                            to="/app/reports/long-outstanding"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/reports/long-outstanding',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/reports/long-outstanding'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Long Outstanding
                        </router-link>

                        <router-link
                            v-show="show_status"
                            class="collapse-item"
                            to="/app/reports/status"
                            :class="{
                                active:
                                    $route.fullPath === '/app/reports/status',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath === '/app/reports/status'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Status
                        </router-link>
                    </div>
                </div>
            </li>
            <li
                class="nav-item"
                v-show="
                    show_rate ||
                    show_fees ||
                    show_company ||
                    show_branch ||
                    show_users
                "
                :class="{
                    active:
                        $route.fullPath === '/app/settings/rate-management' ||
                        $route.fullPath === '/app/settings/company' ||
                        $route.fullPath === '/app/settings/fees-management' ||
                        $route.fullPath === '/app/settings/branch' ||
                        $route.fullPath === '/app/settings/branch/create' ||
                        $route.fullPath === '/app/settings/users' ||
                        $route.fullPath === '/app/settings/user/create' ||
                        $route.fullPath === '/app/settings/biller/create' ||
                        $route.fullPath === '/app/settings/biller/edit' ||
                        $route.fullPath === '/app/settings/billers',
                }"
            >
                <a
                    class="nav-link collapsed"
                    href="#"
                    data-toggle="collapse"
                    data-target="#settings"
                    aria-expanded="true"
                    aria-controls="settings"
                >
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <div
                    id="settings"
                    class="collapse"
                    aria-labelledby="headingTwo"
                    data-parent="#accordionSidebar"
                >
                    <div class="bg-white py-2 collapse-inner rounded">
                        <router-link
                            v-show="show_rate"
                            class="collapse-item"
                            to="/app/settings/rate-management"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/settings/rate-management',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/settings/rate-management'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Rate Management
                        </router-link>

                        <router-link
                            v-show="show_fees"
                            class="collapse-item"
                            to="/app/settings/fees-management"
                            :class="{
                                active:
                                    $route.fullPath ===
                                    '/app/settings/fees-management',
                            }"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath ===
                                    '/app/settings/fees-management'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Fees Management
                        </router-link>

                        <router-link
                            v-show="show_users"
                            :class="{
                                active:
                                    $route.fullPath === '/app/settings/users' ||
                                    $route.fullPath ===
                                        '/app/settings/user/create',
                            }"
                            class="collapse-item"
                            to="/app/settings/users"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath === '/app/settings/users' ||
                                    $route.fullPath ===
                                        '/app/settings/user/create'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Users</router-link
                        >

                        <router-link
                            v-show="show_others"
                            class="collapse-item"
                            :class="{
                                active:
                                    $route.fullPath === '/app/settings/others',
                            }"
                            to="/app/settings/others"
                        >
                            <i
                                class="bi"
                                :class="[
                                    $route.fullPath === '/app/settings/others'
                                        ? 'bi-record-circle-fill'
                                        : 'bi-record-circle',
                                ]"
                            ></i>
                            Others</router-link
                        >
                    </div>
                </div>
            </li>
            <li
                class="nav-item"
                :class="{ active: $route.fullPath === '/app/help' }"
            >
                <router-link class="nav-link" to="/app/help">
                    <i class="bi bi-headset"></i>
                    <span>Help</span></router-link
                >
            </li>
        </div>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block" />

        <!-- Sidebar Toggler (Sidebar) -->
        <!-- <div class="text-center d-none d-md-inline">
            <button @click="toggleSidebar" class="rounded-circle border-0" id="sidebarToggle"></button>
        </div> -->
    </ul>
</template>
<script>
export default {
    props: {
        toogleSidebar: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            user: this.$store.state.auth.user,
            toggled: false,
            show_fileUpload: true,
            show_dataEntry: true,
            show_ammendment: true,
            show_inquiry: true,
            show_monitor: true,
            show_pending: true,
            show_proccess: true,
            show_refund: true,
            show_disposition: true,
            show_long: true,
            show_status: true,
            show_rate: true,
            show_fees: true,
            show_company: true,
            show_branch: true,
            show_users: true,
            show_others: true,
            show_billers: true,
        };
    },
    name: "Sidebar",

    methods: {
        toggleSidebar() {
            this.toggled = !this.toggled;
        },
    },
    created() {
        this.show_fileUpload = JSON.stringify(
            this.user.permissions.permissions
        ).includes("file_upload");
        this.show_dataEntry = JSON.stringify(
            this.user.permissions.permissions
        ).includes("data_entry");
        this.show_inquiry = JSON.stringify(
            this.user.permissions.permissions
        ).includes("inquiry");
        this.show_ammendment = JSON.stringify(
            this.user.permissions.permissions
        ).includes("amendment");
        this.show_monitor = JSON.stringify(
            this.user.permissions.permissions
        ).includes("monitor_transactions");
        this.show_pending = JSON.stringify(
            this.user.permissions.permissions
        ).includes("pending_transactions");
        this.show_proccess = JSON.stringify(
            this.user.permissions.permissions
        ).includes("proccessed_transactions");
        this.show_refund = JSON.stringify(
            this.user.permissions.permissions
        ).includes("refund_request");
        this.show_disposition = JSON.stringify(
            this.user.permissions.permissions
        ).includes("disposition_report");
        this.show_long = JSON.stringify(
            this.user.permissions.permissions
        ).includes("long_outstanding_report");
        this.show_status = JSON.stringify(
            this.user.permissions.permissions
        ).includes("status_report");
        this.show_rate = JSON.stringify(
            this.user.permissions.permissions
        ).includes("rate_management");
        this.show_fees = JSON.stringify(
            this.user.permissions.permissions
        ).includes("fees_management");
        this.show_company = JSON.stringify(
            this.user.permissions.permissions
        ).includes("company");
        this.show_branch = JSON.stringify(
            this.user.permissions.permissions
        ).includes("branch");
        this.show_users = JSON.stringify(
            this.user.permissions.permissions
        ).includes("users");
        this.show_others = JSON.stringify(
            this.user.permissions.permissions
        ).includes("others");
        this.show_billers = JSON.stringify(
            this.user.permissions.permissions
        ).includes("billers");
    },
};
</script>
