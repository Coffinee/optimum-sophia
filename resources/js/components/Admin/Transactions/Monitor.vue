<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">
                        MONITOR TRANSACTIONS
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a
                                @click.prevent="setTab('transactions')"
                                class="nav-link"
                                :class="{ active: tabSet == 'transactions' }"
                                aria-current="page"
                                href="#"
                                >TRANSACTIONS</a
                            >
                        </li>
                        <li class="nav-item">
                            <a
                                @click.prevent="setTab('histories')"
                                class="nav-link"
                                :class="{ active: tabSet == 'histories' }"
                                aria-current="page"
                                href="#"
                                >HISTORIES</a
                            >
                        </li>
                    </ul>
                    <div class="row g-1 justify-content-end py-3">
                        <div class="col-md-6">
                            <div class="row justify-content-end">
                                <div class="col-sm-6">
                                    <div class="form-group has-search">
                                        <i
                                            class="bi bi-search form-control-feedback"
                                        ></i>
                                        <input
                                            v-model="search"
                                            type="text"
                                            v-debounce:500ms="searchData"
                                            name="search"
                                            class="form-control"
                                            placeholder="Search"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <ButtonBlack
                                        icon="bi bi-download"
                                        label="Export"
                                    ></ButtonBlack>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 py-2 table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>TXN NO.</th>
                                    <th>DATE</th>
                                    <th>REFERENCE NO.</th>
                                    <th>REMITTER</th>
                                    <th>BENEFICIARY</th>
                                    <th>TRANSACTION TYPE</th>
                                    <th>CURRENCY</th>
                                    <th>AMOUNT</th>
                                    <th>STATUS</th>

                                    <th
                                        v-if="
                                            (user.role == 'verifier' &&
                                                tabSet == 'transactions') ||
                                            (user.role == 'approver' &&
                                                tabSet == 'transactions') ||
                                            (user.role == 'andor' &&
                                                tabSet == 'transactions')
                                        "
                                        class="text-center"
                                        style="width: 8%"
                                    >
                                        ACTION
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader
                                    :showLoader="isLoading"
                                    :colLength="9"
                                ></TableLoader>
                                <tr v-if="showNoFound">
                                    <td colspan="9">
                                        <img
                                            class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img"
                                            src="/images/no-data.png"
                                            alt=""
                                        />
                                    </td>
                                </tr>
                                <tr
                                    v-for="item in transactions.data"
                                    :key="item.id"
                                >
                                    <td>{{ item.id }}</td>
                                    <td>
                                        {{
                                            $filters.dateFormat(item.created_at)
                                        }}
                                    </td>
                                    <td>{{ item.reference_number }}</td>

                                    <td>
                                        {{
                                            item.remitter_name
                                                ? getCustomerName(
                                                      item.remitter_name
                                                          .data_entry_elements
                                                  )
                                                : ""
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            item.transaction_type == "CBA" ||
                                            "BP"
                                                ? item.bank_name
                                                : item.beneficiary_name
                                                      ?.data_entry_elements
                                                ? getCustomerName(
                                                      item.beneficiary_name
                                                          .data_entry_elements
                                                  )
                                                : ""
                                        }}
                                    </td>
                                    <td>{{ item.transaction_type }}</td>
                                    <td>{{ item.original_currency }}</td>
                                    <td>{{ item.net_amount }}</td>
                                    <td>
                                        {{
                                            item.status == "for-verification"
                                                ? "FOR VERIFICATION"
                                                : item.status == "for-approval"
                                                ? "FOR APPROVAL"
                                                : "APPROVED"
                                        }}
                                    </td>
                                    <td
                                        v-if="
                                            (user.role == 'verifier' &&
                                                tabSet == 'transactions') ||
                                            (user.role == 'approver' &&
                                                tabSet == 'transactions') ||
                                            (user.role == 'andor' &&
                                                tabSet == 'transactions')
                                        "
                                        class="text-center"
                                    >
                                        <button
                                            type="button"
                                            class="btn-round btn-decline"
                                            @click="
                                                decline(
                                                    item.id,
                                                    user.role == 'verifier'
                                                        ? 'not-verified'
                                                        : 'not-approved'
                                                )
                                            "
                                        >
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        <button
                                            v-if="user.role == 'verifier'"
                                            @click="
                                                approve(item.id, 'for-approval')
                                            "
                                            type="button"
                                            class="btn-round btn-approve ml-1"
                                        >
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button
                                            v-if="user.role == 'approver'"
                                            @click="
                                                approve(item.id, 'approved')
                                            "
                                            type="button"
                                            class="btn-round btn-approve ml-1"
                                        >
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <LaravelVuePagination
                        class="float-right"
                        :data="paginatedTransactions"
                        @pagination-change-page="paginatedData"
                    ></LaravelVuePagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from "vform";
import { Button, HasError, AlertError } from "vform/src/components/bootstrap5";
import { errorMessage, successMessage } from "@/utilities.js";
import LaravelVuePagination from "laravel-vue-pagination";

import Search from "@/components/Misc/Inputs/Search.vue";
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import GroupHalfInput from "@/components/Misc/Inputs/GroupHalfInput.vue";
import RightSideModal from "@/components/Misc/Modals/RightSideModal.vue";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
export default {
    props: {},
    name: "monitor-transactions",
    components: {
        Search,
        ButtonOrange,
        ButtonBlack,
        RightSideModal,
        GroupHalfInput,
        HasError,
        LaravelVuePagination,
        TableLoader,
    },
    data() {
        return {
            transactions: {},
            user: this.$store.state.auth.user,
            search: "",
            isLoading: true,
            showNoFound: false,
            remitterName: "",
            tabSet: "transactions",
            isLoading: true,
            showNoFound: false,
            paginatedTransactions: [],
        };
    },
    methods: {
        loadData(page) {
            axios
                .get("/api/transactions/monitor?page=" + page)
                .then((data) => {
                    this.isLoading = false;
                    this.transactions = data.data.data;
                    this.paginatedTransactions = data.data;
                    this.hasPagination = true;

                    console.log("transactions", this.transactions.data);
                    if (this.transactions.length === 0) {
                        this.showNoFound = true;
                    } else {
                        this.showNoFound = false;
                    }
                })
                .catch((e) => {
                    errorMessage("Opps!", e.message, "top-right");
                });
        },
        loadHistoryData(page) {
            axios
                .get("/api/transactions/monitor/history?page=" + page)
                .then((data) => {
                    this.isLoading = false;
                    this.transactions = data.data.data;
                    this.paginatedTransactions = data.data;
                    this.hasPagination = true;
                    if (this.transactions.length === 0) {
                        this.showNoFound = true;
                    } else {
                        this.showNoFound = false;
                    }
                })
                .catch((e) => {
                    errorMessage("Opps!", e.message, "top-right");
                });
        },
        approve(id, status) {
            Swal.fire({
                title:
                    this.user.role == "verifier"
                        ? "Are you sure to verify this transaction?"
                        : "Are you sure to approve this transaction?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText:
                    this.user.role == "verifier"
                        ? "Yes, verify it!"
                        : "Yes, approve it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    axios
                        .put("/api/transactions/" + id, { status: status })
                        .then((data) => {
                            successMessage(
                                "Success!",
                                this.user.role == "verifier"
                                    ? "Transaction has been verified."
                                    : "Transaction has been approved.",
                                "top-right"
                            );
                            this.loadData();
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });
                }
            });
        },
        decline(id, status) {
            Swal.fire({
                title: "Do you definitely want to reject this transaction?",
                text: "Can you provide the reason for declining?",
                input: "text",
                inputAttributes: {
                    autocapitalize: "off",
                    maxlength: 50,
                },
                showCancelButton: true,
                confirmButtonText: "Submit",
                showLoaderOnConfirm: true,
                preConfirm: (remark) => {
                    axios
                        .put("/api/transactions/" + id, {
                            status: status,
                            remarks: remark,
                        })
                        .then((data) => {
                            this.loadData();
                        })
                        .catch((e) => {
                            Swal.showValidationMessage(`Request failed: ${e}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        "You have successfully declined this transaction",
                        "Thank you! Have a wonderful day!",
                        "success"
                    );
                }
            });
        },
        searchData() {},
        getCustomerName(customer_details) {
            const firstName = customer_details.find(
                (element) => element.label === "FIRST NAME"
            );
            const lastName = customer_details.find(
                (element) => element.label === "LAST NAME"
            );

            const middleName = customer_details.find(
                (element) => element.label === "MIDDLE NAME"
            );

            return (
                lastName.value +
                ", " +
                firstName.value +
                " " +
                middleName.value.charAt(0) +
                "."
            );
        },
        async setTab(tab) {
            this.tabSet = tab;
            this.isLoading = true;
            switch (tab) {
                case "transactions":
                    this.loadData();
                    break;

                case "histories":
                    this.loadHistoryData();
                    break;

                default:
                    this.loadData();
                    break;
            }
        },
    },
    created() {
        this.loadData();
    },
};
</script>
