<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">
                        DATA ENTRY
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="row my-3">
                        <div class="col-md-12 mb-4">
                            <h4 class="form-banner">TRANSACTION TYPE</h4>
                        </div>
                        <div class="col-md-4 ml-3 mb-3">
                            <div class="form-floating vs">
                                <v-select
                                    @option:selected="showForm"
                                    :options="allTrasactionType"
                                    placeholder="SELECT TRANSACTION TYPE"
                                    id="transaction_type"
                                />
                                <label for="transaction_type"
                                    >Transaction Type <span>*</span></label
                                >
                            </div>
                        </div>
                    </div>
                    <div class="my-3">
                        <OTCForm
                            v-if="show_form == 'OTC'"
                            :funds="funds"
                            :service_fee="service_fee"
                            :currencies="currencies"
                            :countries="countries"
                            :refNumber="companyRefNumber"
                            :batch_number="batch_number"
                            :addOrLess="addOrLess"
                            :transactionTypeId="transactionTypeId"
                        ></OTCForm>
                        <CBAForm
                            v-if="show_form == 'CBA'"
                            :funds="funds"
                            :banks="banks"
                            :currencies="currencies"
                            :countries="countries"
                            :refNumber="companyRefNumber"
                            :batch_number="batch_number"
                            :addOrLess="addOrLess"
                            :transactionTypeId="transactionTypeId"
                        ></CBAForm>
                        <DTDForm
                            v-if="show_form == 'DTD'"
                            :funds="funds"
                            :service_fee="service_fee"
                            :currencies="currencies"
                            :countries="countries"
                            :refNumber="companyRefNumber"
                            :batch_number="batch_number"
                            :addOrLess="addOrLess"
                            :transactionTypeId="transactionTypeId"
                        ></DTDForm>
                        <BPForm
                            v-if="show_form == 'BP'"
                            :transactionTypeId="transactionTypeId"
                            :fields="fields"
                            :funds="funds"
                            :billers="billers"
                            :currencies="currencies"
                            :countries="countries"
                            :refNumber="companyRefNumber"
                            :batch_number="batch_number"
                            :addOrLess="addOrLess"
                        ></BPForm>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from "vform";
import { errorMessage, successMessage, warningMessage } from "@/utilities.js";
import { HasError } from "vform/src/components/bootstrap5";
import OTCForm from "@/components/Admin/Transactions/DataEntry/OTCForm.vue";
import CBAForm from "@/components/Admin/Transactions/DataEntry/CBAForm.vue";
import DTDForm from "@/components/Admin/Transactions/DataEntry/DTDForm.vue";
import BPForm from "@/components/Admin/Transactions/DataEntry/BPForm.vue";

export default {
    props: {},
    name: "data-entry",
    components: {
        HasError,
        OTCForm,
        CBAForm,
        DTDForm,
        BPForm,
    },
    data() {
        return {
            user: this.$store.state.auth.user,
            companyRefNumber: "",
            inbountToken: "",
            transaction_type: "",
            show_form: "",
            batch_number: "",
            funds: [],
            service_fee: [],
            billers: [],
            currencies: [],
            banks: [],
            countries: [],
            addOrLess: "",
            allTrasactionType: [],
            form: new Form({
                transaction_type: "",
            }),
            transactionTypeId: "",
        };
    },
    methods: {
        getAllCountries() {
            axios
                .get("/api/address/countries")
                .then((data) => {
                    this.countries = data.data.data;
                })
                .catch(() => {
                    console.log("err");
                });
        },
        async showForm(item) {
            this.batch_number = Math.random()
                .toString(36)
                .slice(2, 10)
                .toUpperCase();
            switch (item.name) {
                case "BP":
                    await axios
                        .get(this.$eosUrl + "/api/outbound/get-billers", {
                            params: { "trasaction-type-id": item.value },
                            headers: {
                                Authorization: "Bearer " + this.inbountToken,
                            },
                        })
                        .then((data) => {
                            this.billers = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });

                    await axios
                        .get(
                            this.$eosUrl + "/api/outbound/get-source-of-funds",
                            {
                                headers: {
                                    Authorization:
                                        "Bearer " + this.inbountToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.funds = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });

                    await axios
                        .get(
                            this.$eosUrl +
                                "/api/outbound/get-branch-currencies",
                            {
                                params: { branch_id: this.user.branch_id },
                                headers: {
                                    Authorization:
                                        "Bearer " + this.inbountToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.currencies = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });
                    this.transactionTypeId = item.value;
                    break;

                case "CBA":
                    await axios
                        .get(this.$eosUrl + "/api/outbound/get-billers", {
                            params: { "trasaction-type-id": item.value },
                            headers: {
                                Authorization: "Bearer " + this.inbountToken,
                            },
                        })
                        .then((data) => {
                            this.banks = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });

                    await axios
                        .get(
                            this.$eosUrl + "/api/outbound/get-source-of-funds",
                            {
                                headers: {
                                    Authorization:
                                        "Bearer " + this.inbountToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.funds = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });

                    await axios
                        .get(
                            this.$eosUrl +
                                "/api/outbound/get-branch-currencies",
                            {
                                params: { branch_id: this.user.branch_id },
                                headers: {
                                    Authorization:
                                        "Bearer " + this.inbountToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.currencies = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });
                    this.transactionTypeId = item.value;
                    break;

                default:
                    await axios
                        .get(this.$eosUrl + "/api/outbound/get-billers", {
                            params: { "trasaction-type-id": item.value },
                            headers: {
                                Authorization: "Bearer " + this.inbountToken,
                            },
                        })
                        .then((data) => {
                            this.service_fee = data.data.data[0].currency;
                        })
                        .catch((e) => {
                            errorMessage("Opps s!", e.message, "top-right");
                        });

                    await axios
                        .get(
                            this.$eosUrl + "/api/outbound/get-source-of-funds",
                            {
                                headers: {
                                    Authorization:
                                        "Bearer " + this.inbountToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.funds = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });

                    await axios
                        .get(
                            this.$eosUrl +
                                "/api/outbound/get-branch-currencies",
                            {
                                params: { branch_id: this.user.branch_id },
                                headers: {
                                    Authorization:
                                        "Bearer " + this.inbountToken,
                                },
                            }
                        )
                        .then((data) => {
                            this.currencies = data.data.data;
                        })
                        .catch((e) => {
                            errorMessage("Opps!", e.message, "top-right");
                        });
                    break;
            }
            this.show_form = item.name;
            this.transactionTypeId = item.value;
        },
    },
    created() {
        this.batch_number = Math.random()
            .toString(36)
            .slice(2, 10)
            .toUpperCase();
        this.getAllCountries();
        axios
            .get("/api/get-inbound-api-token")
            .then((data) => {
                this.inbountToken = data.data.token;
            })
            .finally(async (e) => {
                await axios
                    .get(this.$eosUrl + "/api/outbound/get-transaction-types", {
                        headers: {
                            Authorization: "Bearer " + this.inbountToken,
                        },
                    })
                    .then((data) => {
                        this.allTrasactionType = data.data.data;
                    })
                    .catch((e) => {
                        errorMessage("Opps!", e.message, "top-right");
                    });

                await axios
                    .get(this.$eosUrl + "/api/outbound/get-branch-details", {
                        params: { "branch-id": this.user.branch_id },
                        headers: {
                            Authorization: "Bearer " + this.inbountToken,
                        },
                    })
                    .then((data) => {
                        this.companyRefNumber = data.data.data.reference_number;
                        this.addOrLess = data.data.data.type_of_charging;
                    })
                    .catch((e) => {
                        errorMessage("Opps!", e.message, "top-right");
                    });
            });
    },
};
</script>
