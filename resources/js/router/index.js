import { createWebHistory, createRouter } from "vue-router";
import store from "@/store";
import axios from "axios";

/* Guest Component */
const Home = () => import("@/components/Guest/Home.vue");
const Login = () => import("@/components/Guest/Login.vue");
const Register = () => import("@/components/Guest/Register.vue");
const NotFound = () => import("@/components/Guest/NotFound.vue");
const ResetPassword = () => import("@/components/Guest/ResetPassword.vue");
const ForgotPassword = () => import("@/components/Guest/ForgotPassword.vue");
const PartnerCode = () => import("@/components/Guest/PartnerCode.vue");
/* Guest Component */

/* Layouts */
const DahboardLayout = () => import("@/components/Layouts/Default.vue");
const GuestLayout = () => import("@/components/Layouts/Guest.vue");
/* Layouts */

/* Authenticated Component */
const Dashboard = () => import("@/components/Admin/Dashboard/Dashboard.vue");
const BatchUpload = () =>
    import("@/components/Admin/Transactions/BatchUpload.vue");
const DataEntry = () => import("@/components/Admin/Transactions/DataEntry.vue");
const Monitor = () => import("@/components/Admin/Transactions/Monitor.vue");
const Pending = () => import("@/components/Admin/Transactions/Pending.vue");
const Proccessed = () =>
    import("@/components/Admin/Transactions/Proccessed.vue");
const RefundRequest = () =>
    import("@/components/Admin/Transactions/RefundRequest.vue");
const Amendment = () => import("@/components/Admin/Amendment/Amendment.vue");
const Inquiry = () => import("@/components/Admin/Inquiry/Inquiry.vue");
const Disposition = () => import("@/components/Admin/Reports/Disposition.vue");
const LongOutstanding = () =>
    import("@/components/Admin/Reports/LongOutstanding.vue");
const Status = () => import("@/components/Admin/Reports/Status.vue");
const Rate = () => import("@/components/Admin/Settings/Rate.vue");
const Fees = () => import("@/components/Admin/Settings/Fees.vue");
const Company = () => import("@/components/Admin/Settings/Company.vue");
const Branch = () => import("@/components/Admin/Settings/Branch.vue");
const Users = () => import("@/components/Admin/Settings/Users.vue");
const UserForm = () => import("@/components/Admin/Settings/Users/Form.vue");
const Others = () => import("@/components/Admin/Settings/Others.vue");
const Billers = () => import("@/components/Admin/Settings/Billers.vue");
const BillersForm = () => import("@/components/Admin/Settings/Biller/Form.vue");
const BranchCreate = () => import("@/components/Admin/Settings/Branch/Form.vue");
const Help = () => import("@/components/Admin/Help/Help.vue");
const Receipt = () => import("@/components/Admin/Others/Receipt.vue");
const Unauthorized = () => import("@/components/Admin/Others/Unauthorized.vue");
/* Authenticated Component */

const routes = [
    {
        path: "/",
        component: GuestLayout,
        meta: {
            middleware: "guest",
        },
        children: [
            {
                name: "home",
                path: "/",
                component: Home,
                meta: {
                    title: `Home`,
                },
            },
            {
                name: "login",
                path: "/login",
                component: Login,
                meta: {
                    title: `Login`,
                },
            },
            {
                name: "register",
                path: "/register",
                component: Register,
                meta: {
                    title: `Register`,
                },
            },
            {
                name: "NotFound",
                path: "/:catchAll(.*)",
                component: NotFound,
                meta: {
                    title: `NotFound`,
                },
            },
            {
                name: "ForgotPassword",
                path: "/forgot-password",
                component: ForgotPassword,
                meta: {
                    title: `Forgot Password`,
                },
            },
            {
                name: "PartnerCode",
                path: "/partner-code",
                component: PartnerCode,
                meta: {
                    title: `Partner Code`,
                },
            },
            {
                name: "ResetPassword",
                path: "/reset-password",
                component: ResetPassword,
                meta: {
                    title: `Reset Password`,
                },
            },
        ],
    },

    {
        path: "/app",
        component: DahboardLayout,
        meta: {
            middleware: "auth",
        },
        children: [
            {
                name: "dashboard",
                path: "/app/dashboard",
                component: Dashboard,
                meta: {
                    title: `Dashboard`,
                    ability: "",
                },
            },
            {
                name: "batch-upload",
                path: "/app/transaction/batch-upload",
                component: BatchUpload,
                meta: {
                    title: `Batch Transaction`,
                    ability: "file_upload",
                },
            },
            {
                name: "data-entry",
                path: "/app/transaction/data-entry",
                component: DataEntry,
                meta: {
                    title: `Data Entry`,
                    ability: "data_entry",
                },
            },
            {
                name: "amendment",
                path: "/app/amendment",
                component: Amendment,
                meta: {
                    title: `Amendment`,
                    ability: "amendment",
                },
            },
            {
                name: "inquiry",
                path: "/app/inquiry",
                component: Inquiry,
                meta: {
                    title: `Inquiry`,
                    ability: "inquiry",
                },
            },
            {
                name: "monitor-transactions",
                path: "/app/transactions/monitor",
                component: Monitor,
                meta: {
                    title: `Monitor Transactions`,
                    ability: "monitor_transactions",
                },
            },
            {
                name: "pending-transactions",
                path: "/app/transactions/pending",
                component: Pending,
                meta: {
                    title: `Pending Transactions`,
                    ability: "pending_transactions",
                },
            },
            {
                name: "proccessed-transactions",
                path: "/app/transactions/proccessed",
                component: Proccessed,
                meta: {
                    title: `Proccessed Transactions`,
                    ability: "proccessed_transactions",
                },
            },
            {
                name: "refund-request",
                path: "/app/transactions/refund-request",
                component: RefundRequest,
                meta: {
                    title: `Refund Request`,
                    ability: "refund_request",
                },
            },
            {
                name: "disposition-report",
                path: "/app/reports/disposition",
                component: Disposition,
                meta: {
                    title: `Disposition Report`,
                    ability: "disposition_report",
                },
            },
            {
                name: "long-outstanding-report",
                path: "/app/reports/long-outstanding",
                component: LongOutstanding,
                meta: {
                    title: `Long Outstanding Report`,
                    ability: "long_outstanding_report",
                },
            },
            {
                name: "status-report",
                path: "/app/reports/status",
                component: Status,
                meta: {
                    title: `Status Report`,
                    ability: "status_report",
                },
            },
            {
                name: "rate-management",
                path: "/app/settings/rate-management",
                component: Rate,
                meta: {
                    title: `Rate Management`,
                    ability: "rate_management",
                },
            },
            {
                name: "fees-management",
                path: "/app/settings/fees-management",
                component: Fees,
                meta: {
                    title: `Fees Management`,
                    ability: "fees_management",
                },
            },
            {
                name: "company",
                path: "/app/settings/company",
                component: Company,
                meta: {
                    title: `Company`,
                    ability: "company",
                },
            },
            {
                name: "branch",
                path: "/app/settings/branch",
                component: Branch,
                meta: {
                    title: `Branch`,
                    ability: "branch",
                },
            },
            {
                name: "users",
                path: "/app/settings/users",
                component: Users,
                meta: {
                    title: `Users`,
                    ability: "users",
                },
            },
            {
                name: "create-user",
                path: "/app/settings/user/create",
                component: UserForm,
                meta: {
                    title: `Create User`,
                    ability: "users",
                },
            },
            {
                name: "edit-user",
                path: "/app/settings/user/edit",
                component: UserForm,
                meta: {
                    title: `Edit User`,
                    ability: "users",
                },
            },
            {
                name: "create-branch",
                path: "/app/settings/branch/create",
                component: BranchCreate,
                meta: {
                    title: `Create Branch`,
                    ability: "branch",
                },
            },

            {
                name: "edit-branch",
                path: "/app/settings/branch/edit",
                component: BranchCreate,
                meta: {
                    title: "Edit Branch",
                    ability: "branch",
                },
            },
            {
                name: "billers",
                path: "/app/settings/billers",
                component: Billers,
                meta: {
                    title: `Billers`,
                    ability: "billers",
                },
            },
            {
                name: "billers-create",
                path: "/app/settings/biller/create",
                component: BillersForm,
                meta: {
                    title: `Create Biller`,
                    ability: 'billers',
                },
            },
            {
                name: "billers-edit",
                path: "/app/settings/biller/edit",
                component: BillersForm,
                meta: {
                    title: `Update Biller`,
                    ability: 'billers',
                },
            },
            {
                name: "others",
                path: "/app/settings/others",
                component: Others,
                meta: {
                    title: `Others`,
                    ability: "others",
                },
            },
            {
                name: "help",
                path: "/app/help",
                component: Help,
                meta: {
                    title: `Help`,
                    ability: "",
                },
            },
            {
                name: "receipt",
                path: "/app/receipt",
                component: Receipt,
                meta: {
                    title: `Receipt`,
                    ability: "",
                },
            },
            {
                name: "unauthorized",
                path: "/app/unauthorized",
                component: Unauthorized,
                meta: {
                    title: `401 | Unauthorized`,
                    ability: "unauthorized",
                },
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes, // short for `routes: routes`
    watch: {
        $route(to) {
            if (to.currentRoute.meta.reload == true) {
                window.location.reload();
            }
        },
    },
    scrollBehavior() {
        document.getElementById("app").scrollIntoView();
    },
});

router.beforeEach((to, from, next) => {
    document.title = to.meta.title;
    if (to.meta.middleware == "guest") {
        if (store.state.auth.authenticated) {
            next({ name: "dashboard" });
        }
        next();
    } else {
        // console.log(store.state.auth.authenticated)
        // if (store.state.auth.authenticated) {
        //     axios
        //         .get("/api/check-permission", {
        //             params: {
        //                 ability: to.meta.ability,
        //             },
        //         })
        //         .then((data) => {
        //             if (data.status) {
        //                 next();
        //             } else {
        //                 if (to.meta.ability == "") {
        //                     next();
        //                 } else {
        //                     Swal.fire({
        //                         title: "Opps!",
        //                         text: "You are not authorized to perform this action!",
        //                         icon: "error",
        //                         showCancelButton: false,
        //                         confirmButtonColor: "#3085d6",
        //                         cancelButtonColor: "#d33",
        //                         confirmButtonText: "Go back to Dashboard",
        //                     }).then((result) => {
        //                         if (result.isConfirmed) {
        //                             next({ name: "dashboard" });
        //                         }
        //                     });
        //                 }
        //             }
        //         })
        //         .catch((e) => {
        //             console.log(e.message);
        //             // if (e.response.status == 401) {
        //             //     axios.post("/logout").then(({ data }) => {
        //             //         localStorage.removeItem("vuex");
        //             //     });
        //             // }
        //         })
        // } else {
        //     next({ name: "login" });
        // }
       // next({ name: "login" });
        next();
    }
});

export default router;
