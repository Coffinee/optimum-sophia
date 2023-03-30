<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 text-right text-white">FEES MANAGEMENT</h6>
                </div>
                <div class="card-body">
                    <div class="row g-1 justify-content-between py-3">
                        <div class="col-md-3">
                            <div class="form-floating vs">
                                <v-select
                                    v-model="currency"
                                    @option:selected="getBillers"
                                    :options="transactionTypes"
                                    id="currency"
                                    placeholder="SELECT TYPE"
                                />
                                <label for="currency"
                                    >Transaction type <span>*</span></label
                                >
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating vs">
                                <v-select
                                    v-model="bulk_action"
                                    @option:selected="fireMe"
                                    :options="actions"
                                    id="currency"
                                    placeholder="SELECT ACTION"
                                />
                                <label for="currency"
                                    >Bulk Action <span>*</span></label
                                >
                            </div>
                        </div>
                        <div class="col-md-1">
                            <ButtonOrange
                                class="thin-pad"
                                icon="bi bi-plus-circle"
                                @click.prevent="applyBulkAction"
                                label="Apply"
                            ></ButtonOrange>
                        </div>
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
                                            v-debounce:500ms="searchFees"
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
                                    <th v-if="user.role == 'admin'">COMPANY</th>
                                    <th>TRANSACTION TYPE</th>
                                    <th>BANK NAME/ BILLER NAME</th>
                                    <th>AMOUNT RANGE</th>
                                    <th>CURRENCY</th>
                                    <th>TYPE OF FEE</th>
                                    <th style="width: 220px">SERVICE FEE</th>
                                    <th>LAST DATE UPDATED</th>
                                    <th>UPDATED BY</th>
                                    <th style="width: 5%" class="text-center">
                                        ACTION
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader
                                    :showLoader="isLoading"
                                    :colLength="6"
                                ></TableLoader>
                                <tr v-if="showNoFound">
                                    <td colspan="7">
                                        <img
                                            class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img"
                                            src="/images/no-data.png"
                                            alt=""
                                        />
                                    </td>
                                </tr>
                                <tr v-for="item in fees.data" :key="item.id">
                                    <td v-if="user.role == 'admin'">
                                        {{
                                            item.company_id != null
                                                ? item.company_id
                                                : ""
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            item.biller.biller_category_name
                                                .transaction_name == null
                                                ? item.biller
                                                      .biller_category_name
                                                      .meta_value
                                                : item.biller
                                                      .biller_category_name
                                                      .transaction_name
                                                      .meta_value
                                        }}
                                    </td>
                                    <td>{{ item.biller.name }}</td>
                                    <td>
                                        {{
                                            item.amount_range
                                                ? item.amount_range.replace(
                                                      /"/g,
                                                      ""
                                                  )
                                                : ""
                                        }}
                                    </td>
                                    <td>{{ item.currency }}</td>
                                    <td style="padding: 2px">
                                        <div
                                            class="input-group rate-input"
                                            v-show="selectedRow == item.id"
                                        >
                                            <v-select
                                                :options="[
                                                    { label: 'FIXED AMOUNT' },
                                                    { label: 'PERCENTAGE' },
                                                ]"
                                                v-model="form.type_of_fee"
                                            ></v-select>
                                        </div>
                                        <span v-show="selectedRow != item.id">
                                            {{ item.type_of_fee }}</span
                                        >
                                    </td>
                                    <td style="padding: 2px">
                                        <div
                                            class="input-group rate-input"
                                            v-show="selectedRow == item.id"
                                        >
                                            <input
                                                type="text"
                                                @keyup.enter="
                                                    updateFee(item.id, $event)
                                                "
                                                @keypress="isNumber()"
                                                class="form-control"
                                                :disabled="disabled"
                                                :class="'rate-input-' + item.id"
                                                :value="item.amount"
                                            />
                                            <button
                                                @click.prevent="
                                                    updateRateButton(
                                                        item.id,
                                                        form.type_of_fee
                                                    )
                                                "
                                                class="btn"
                                                type="button"
                                                :disabled="disabled"
                                            >
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </div>
                                        <span v-show="selectedRow != item.id">
                                            {{ item.service_fee }}</span
                                        >
                                    </td>
                                    <td>
                                        {{
                                            $filters.dateFormat(item.updated_at)
                                        }}
                                    </td>
                                    <td>
                                        {{ item.last_updated_by }}
                                        {{ item.last_updated_by }} -
                                        {{ $filters.capitalize(user.role) }}
                                    </td>
                                    <td
                                        v-if="user.role != 'admin'"
                                        class="text-center d-flex justify-content-center"
                                    >
                                        <a
                                            @click.prevent="editFee(item.id)"
                                            class="rounded-s-button"
                                            href="#"
                                        >
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <LaravelVuePagination
                        class="float-right"
                        :data="fees"
                        @pagination-change-page="getFees"
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
    name: "fees-management",

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
            isLoading: true,
            outboundToken: "",
            inboundToken: "",
            transactionTypes: [],
            showNoFound: false,

            fees: {},
            currency: "",
            bulk_action: "",
            actions: [
                { label: "Bulk Create", value: "bulk-create" },
                { label: "Bulk Edit", value: "bulk-edit" },
            ],
            search: "",
            user: this.$store.state.auth.user,
            disabled: true,
            disableMouseLeave: false,
            selectedRow: null,
            form: new Form({
                type_of_fee: "",
            }),
        };
    },
    methods: {
        searchFees() {
            console.log("asd");
        },
        getBillers(data) {},
        applyBulkAction() {},

        editFee(id) {
            this.disabled = false;
            if (this.selectedRow == id) {
                this.showInput("");
            } else {
                this.selectedRow = id;
            }
        },
        isNumber: function (evt) {
            evt = evt ? evt : window.event;
            var charCode = evt.which ? evt.which : evt.keyCode;
            if (
                charCode > 31 &&
                (charCode < 48 || charCode > 57) &&
                charCode !== 46
            ) {
                evt.preventDefault();
                errorMessage("Opps!", "Please enter number only!", "top-right");
            } else {
                return true;
            }
        },
        showInput(id) {
            if (id == null) this.disabled = true;
            this.selectedRow = id;
        },
        updateRateButton(id, type_of_fee) {
            var amount = $(".rate-input-" + id).val();
            Swal.fire({
                title: "Are you sure to update this service fee?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.$Progress.start();
                    await axios
                        .put(
                            this.$eosUrl + "/api/inbound/fees/" + id,
                            {
                                amount: amount,
                                user_id: this.user.id,
                                type_of_fee: type_of_fee.label,
                            },
                            {
                                headers: {
                                    Authorization:
                                        "Bearer " + this.outboundToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.getFees();
                            successMessage(
                                "Updated!",
                                "Service fee has been updated!",
                                "top-right"
                            );
                            this.showInput("");
                            this.$Progress.finish();
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                            this.$Progress.fail();
                        });
                }
            });
        },
        updateFee(id, event) {
            var amount = event.target.value;
            Swal.fire({
                title: "Are you sure to update this service fee?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.$Progress.start();
                    await this.form
                        .put(
                            this.$eosUrl + "/api/inbound/fees/" + id,
                            {
                                amount: amount,
                                user_id: this.user.id,
                            },
                            {
                                headers: {
                                    Authorization:
                                        "Bearer " + this.outboundToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.getFees();
                            successMessage(
                                "Updated!",
                                "Service fee has been updated!",
                                "top-right"
                            );
                            this.showInput("");
                            this.$Progress.finish();
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                            this.$Progress.fail();
                        });
                }
            });
        },
        async getFees(page = 1) {
            await axios
                .get(this.$eosUrl + "/api/outbound/fees?page=" + page, {
                    params: {
                        company_id: this.user.company_id,
                        branch_id: this.user.branch_id,
                        currency: "PHP",
                        page: page,
                    },
                    headers: { Authorization: "Bearer " + this.inboundToken },
                })
                .then((data) => {
                    this.showNoFound = data.data.data.total == 0 ? true : false;
                    this.fees = data.data.data;
                    this.isLoading = false;
                })
                .catch((e) => {
                    errorMessage("Opps!", e.message, "top-right");
                });
        },
    },
    created() {
        $(document).ready(function () {
            $(".vs__clear").click(function () {
                axios.get("/api/get-outbound-api-token").then((data) => {
                    this.outboundToken = data.data.token;
                });
            });
        });
        axios.get("/api/get-outbound-api-token").then((data) => {
            this.outboundToken = data.data.token;
        });
        axios
            .get("/api/get-inbound-api-token")
            .then((data) => {
                this.inboundToken = data.data.token;
            })
            .finally(async (e) => {
                await axios
                    .get(this.$eosUrl + "/api/outbound/fees", {
                        params: {
                            company_id: this.user.company_id,
                            branch_id: this.user.branch_id,
                            currency: "PHP",
                        },
                        headers: {
                            Authorization: "Bearer " + this.inboundToken,
                        },
                    })
                    .then((data) => {
                        this.showNoFound =
                            data.data.data.total == 0 ? true : false;
                        this.fees = data.data.data;
                        this.isLoading = false;
                    })
                    .catch((e) => {
                        errorMessage("Opps!", e.message, "top-right");
                    });
            });
    },
};
</script>
