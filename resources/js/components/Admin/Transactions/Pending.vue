<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">
                        PENDING TRANSACTIONS
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
                    <div class="row justify-content-end py-3">
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
                                        label="EXPORT"
                                    ></ButtonBlack>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <v-select
                                v-model="formBulk.selectedAction"
                                :options="bulkActions"
                                placeholder="BULK ACTIONS"
                            />
                        </div>
                        <div class="col-md-2">
                            <ButtonOrange
                                icon="bi bi-download"
                                label="APPLY"
                                @click="updateBulk()"
                            ></ButtonOrange>
                        </div>
                    </div>
                    <div class="col-md-12 py-2 table-responsive">
                        <table class="table table-hover">
                            <thead class="table-active">
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            v-model="selectAll"
                                        />
                                    </td>
                                    <th>BATCH NUMBER</th>
                                    <th>FILE NAME</th>
                                    <th>UPLOADED BY</th>
                                    <th>EMAIL ADDRESS</th>
                                    <th>ITEM COUNT</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>EXCHANGE RATE</th>
                                    <th>DATE UPLOADED</th>
                                    <th>STATUS</th>
                                    <th v-show="tabSet == 'transactions'">
                                        ACTIONS
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader
                                    :showLoader="isLoading"
                                    :colLength="11"
                                ></TableLoader>
                                <tr v-if="showNoFound">
                                    <td colspan="11">
                                        <img
                                            class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img"
                                            src="/images/no-data.png"
                                            alt=""
                                        />
                                    </td>
                                </tr>
                                <tr
                                    v-for="transaction in transactions"
                                    :key="transaction.id"
                                >
                                    <td>
                                        <input
                                            type="checkbox"
                                            :value="transaction.batch_number"
                                            v-model="
                                                formBulk.selectedBatchNumber
                                            "
                                        />
                                    </td>
                                    <td>{{ transaction.batch_number }}</td>
                                    <td>{{ transaction.filename }}</td>
                                    <td>{{ transaction.uploaded_by }}</td>
                                    <td>{{ transaction.email_address }}</td>
                                    <td>{{ transaction.total_count }}</td>
                                    <td>{{ transaction.total_amount }}</td>
                                    <td>{{ transaction.exchange_rate }}</td>
                                    <td>{{ transaction.date_uploaded }}</td>
                                    <td>{{ transaction.status }}</td>
                                    <td>
                                        <!-- not yet working -->
                                        <div
                                            class=""
                                            v-show="tabSet == 'transactions'"
                                        >
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger rounded-circle"
                                                height="18px"
                                                width="18px"
                                                @click="
                                                    declineBatch(
                                                        transaction.batch_number
                                                    )
                                                "
                                            >
                                                <i class="bi bi-x-circle"></i>
                                            </button>

                                            <button
                                                type="button"
                                                v-show="
                                                    this.user.role != 'maker'
                                                "
                                                class="btn btn-sm btn-success rounded-circle ml-1"
                                                height="18px"
                                                width="18px"
                                                @click="
                                                    updateStatus(
                                                        transaction.batch_number,
                                                        transaction.status
                                                    )
                                                "
                                            >
                                                <i
                                                    class="bi bi-check2-circle"
                                                ></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-active" v-show="!showNotFound">
                                <tr>
                                    <!-- <td></td> -->
                                    <td colspan="11">
                                        <div
                                            style="display: grid; float: right"
                                        >
                                            <span>
                                                TOTAL COUNT:
                                                {{ displayTotalCount }}
                                            </span>

                                            <span>
                                                TOTAL AMOUNT:
                                                {{
                                                    displayTotalAmount.toLocaleString()
                                                }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
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
import LaravelVuePagination from "laravel-vue-pagination"; //SHOULD BE IN APP.JS

import Search from "@/components/Misc/Inputs/Search.vue";
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import GroupHalfInput from "@/components/Misc/Inputs/GroupHalfInput.vue";
import RightSideModal from "@/components/Misc/Modals/RightSideModal.vue";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
export default {
    props: {},
    name: "pending-transactions",
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
            user: this.$store.state.auth.user,
            search: "",
            isLoading: true,
            showNoFound: false,
            transactions: [],
            paginatedTransactions: [],
            hasPagination: true,
            bulkActions: ["APPROVE", "VERIFY", "DECLINE"],
            form: new Form({
                batchNumber: "",
                status: "",
                remarks: "",
            }),
            formBulk: new Form({
                selectedBatchNumber: [],
                selectedAction: "",
            }),
        };
    },
    methods: {
        declineBatch(batch_number) {
            this.form.batchNumber = batch_number;
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
                    this.form.remarks = remark;
                    this.form
                        .delete("/api/transactions/pending/decline/")
                        .then((data) => {
                            this.paginatedData();
                        })
                        .catch((e) => {
                            Swal.showValidationMessage(`Request failed: ${e}`);
                        });
                    this.form.batchNumber = batch_number;
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

            // this.formBulk
            //         .delete("/api/transactions/pending/decline/")
            //         .then((data) => {
            //             this.isLoading = false;
            //             this.paginatedData();
            //             console.log(data);
            //         })
            //         .catch((err) => {
            //             console.log(err.message);
            //         });
            // }
        },
        declineBulk() {
            if (this.formBulk.selectedBatchNumber.length != 0) {
                this.bulkDisabled = false;
                console.log(this.formBulk.selectedBatchNumber);
                this.formBulk
                    .delete("/api/transactions/pending/decline/")
                    .then((data) => {
                        this.isLoading = false;
                        this.paginatedData();
                        console.log(data);
                    })
                    .catch((err) => {
                        console.log(err.message);
                    });
            }
        },
        updateStatus(batch_number, status) {
            this.form.batchNumber = batch_number;
            this.form.status = status;

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
                    this.form
                        .post("/api/transactions/pending/status")
                        .then((data) => {
                            successMessage(
                                "Success!",
                                this.user.role == "verifier"
                                    ? "Transaction has been verified."
                                    : "Transaction has been approved.",
                                "top-right"
                            );
                            this.paginatedData();
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });
                }
            });
        },
        updateBulk() {
            if (this.formBulk.selectedBatchNumber.length != 0) {
                this.bulkDisabled = false;

                if (this.formBulk.selectedAction == "DECLINE") {
                    // this.formBulk
                    //     .delete("/api/transactions/pending/decline/bulk")
                    //     .then((data) => {
                    //         this.isLoading = false;
                    //         this.paginatedData();
                    //         console.log(data);
                    //     })
                    //     .catch((err) => {
                    //         console.log(err.message);
                    //     });

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
                            this.form.remarks = remark;
                            this.formBulk
                                .delete(
                                    "/api/transactions/pending/decline/bulk"
                                )
                                .then((data) => {
                                    this.isLoading = false;
                                    this.paginatedData();
                                })
                                .catch((e) => {
                                    Swal.showValidationMessage(
                                        `Request failed: ${e}`
                                    );
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
                }

                if (
                    this.formBulk.selectedAction == "APPROVE" ||
                    this.formBulk.selectedAction == "VERIFY"
                ) {
                    // this.formBulk
                    //     .put("/api/transactions/pending/status/bulk")
                    //     .then((data) => {
                    //         this.isLoading = false;
                    //         this.paginatedData();
                    //         console.log(data);
                    //     })
                    //     .catch((err) => {
                    //         console.log(err.message);
                    //     });

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
                            this.formBulk
                                .put("/api/transactions/pending/status/bulk")
                                .then((data) => {
                                    successMessage(
                                        "Success!",
                                        this.user.role == "verifier"
                                            ? "Transaction has been verified."
                                            : "Transaction has been approved.",
                                        "top-right"
                                    );
                                    this.isLoading = false;
                                    this.paginatedData();
                                })
                                .catch((e) => {
                                    errorMessage(
                                        "Opps!",
                                        e.message,
                                        "top-right"
                                    );
                                });
                        }
                    });
                }
            }
        },
        searchData() {},
        paginatedData(page) {
            setTimeout(() => {
                axios
                    .get("/api/transactions/pending?page=" + page)
                    .then((data) => {
                        this.isLoading = false;
                        this.formBulk.selectedBatchNumber = [];
                        this.transactions = data.data.data.data;
                        this.paginatedTransactions = data.data.data;

                        if (page > 1) {
                            this.transactions = this.paginatedTransactions.data;
                            // console.log(
                            //   "page" + page,
                            //   this.transactions,
                            //   this.paginatedTransactions
                            // );
                        }

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
            }, 500);
        },
        sumArrayByPage(colName, arrayData) {
            let data = [];

            if (!Array.isArray(arrayData)) {
                for (let [key, value] of Object.entries(arrayData)) {
                    if (colName == "total_count") {
                        value = value.total_count;
                    } else if (colName == "total_amount") {
                        value = value.total_amount;
                    }
                    data.push(value);
                }
            } else {
                arrayData.map(function (value) {
                    if (colName == "total_count") {
                        value = value.total_count;
                    } else if (colName == "total_amount") {
                        value = value.total_amount;
                    }
                    data.push(value);
                });
            }

            return data.reduce((total, item) => {
                if (colName == "total_amount") {
                    item = Number(item.replace(/,/g, ""));
                }
                return total + item;
            }, 0);
        },
        loadHistoryData(page) {
            axios
                .get("/api/transactions/pending/history?page=" + page)

                .then((data) => {
                    this.isLoading = false;

                    this.paginatedTransactions = data.data.data;
                    this.transactions = this.paginatedTransactions.data;
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
        async setTab(tab) {
            this.tabSet = tab;
            this.isLoading = true;
            switch (tab) {
                case "transactions":
                    this.paginatedData();
                    break;

                case "histories":
                    this.loadHistoryData();

                    break;

                default:
                    this.paginatedData();
                    this.isLoading = false;
                    break;
            }
        },
    },
    computed: {
        displayTotalCount() {
            return this.sumArrayByPage("total_count", this.transactions);
        },

        displayTotalAmount() {
            return this.sumArrayByPage("total_amount", this.transactions);
        },
        selectAll: {
            get() {
                return this.transactions
                    ? this.formBulk.selectedBatchNumber.length ==
                          this.transactions.length
                    : false;
            },
            set(value) {
                var selected = [];

                if (value) {
                    if (Array.isArray(this.transactions)) {
                        this.transactions.forEach(function (transaction) {
                            selected.push(transaction.batch_number);
                        });
                    } else {
                        for (let [key, value] of Object.entries(
                            this.transactions
                        )) {
                            selected.push(value.batch_number);
                        }
                    }
                }

                this.formBulk.selectedBatchNumber = selected;
            },
        },
    },
    created() {
        this.paginatedData();
    },
};
</script>
