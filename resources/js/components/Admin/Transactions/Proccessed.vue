<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">
                        PROCCESSED TRANSACTIONS
                    </h6>
                </div>
                <div class="card-body">
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
                                    <th>STATUS</th>
                                    <th>BATCH NUMBER</th>
                                    <th>FILENAME</th>
                                    <th>UPLOADER NAME</th>
                                    <th>EMAIL ADDRESS</th>
                                    <th>ITEM COUNT</th>
                                    <th>CURRENCY</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>EXCHANGE RATE</th>
                                    <th>DATE/TIME UPLOADED</th>
                                    <th>DATE/TIME PROCCESSED</th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader
                                    :showLoader="isLoading"
                                    :colLength="10"
                                ></TableLoader>
                                <tr
                                    v-for="transaction in transactions"
                                    :key="transaction.id"
                                >
                                    <td>{{ transaction.status }}</td>
                                    <td>{{ transaction.batch_number }}</td>
                                    <td>
                                        {{
                                            transaction.filename == ""
                                                ? "None"
                                                : transaction.filename
                                        }}
                                    </td>
                                    <td>{{ transaction.uploaded_by }}</td>
                                    <td>{{ transaction.email_address }}</td>
                                    <td>{{ transaction.total_count }}</td>
                                    <td>{{ transaction.original_currency }}</td>
                                    <td>{{ transaction.total_amount }}</td>
                                    <td>{{ transaction.exchange_rate }}</td>
                                    <td>{{ transaction.date_uploaded }}</td>
                                </tr>

                                <tr v-if="showNoFound">
                                    <td colspan="11">
                                        <img
                                            class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img"
                                            src="/images/no-data.png"
                                            alt=""
                                        />
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
                        @pagination-change-page="loadData"
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
    name: "proccessed-transactions",
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
            inboundToken: "",
        };
    },
    methods: {
        loadData(page) {
            setTimeout(() => {
                axios
                    .get("/api/get-transaction-inbound-api-token")
                    .then((data) => {
                        this.inboundToken = data.data.token;
                        console.log(this.inboundToken);
                    })
                    .finally(async (e) => {
                        await axios
                            .get(
                                this.$eosUrl +
                                    "/api/transaction-outbound/processed?page=" +
                                    page,
                                {
                                    headers: {
                                        Authorization:
                                            "Bearer " + this.inboundToken,
                                    },
                                }
                            )
                            .then((data) => {
                                this.isLoading = false;
                                this.transactions = data.data.data.data;
                                this.paginatedTransactions = data.data.data;
                                // console.log("log", data.data.data.data);
                            })
                            .catch((e) => {
                                errorMessage("Opps!", e.message, "top-right");
                            });
                    });
            }, 500);
        },
        searchData() {},
    },
    created() {
        this.loadData();
    },
};
</script>
