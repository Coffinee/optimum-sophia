<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">
                        RECEIPT
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="receipt-holder" id="printMe">
                                <div
                                    class="row align-content-center align-items-center"
                                >
                                    <div class="col-md-7">
                                        <img
                                            class="img-fluid logo"
                                            :src="company_logo"
                                            alt=""
                                        />
                                    </div>
                                    <div class="col-md-5">
                                        <p class="mb-0">Tel. No. 455-4566</p>
                                        <p class="mb-0">
                                            {{ company_address }} -
                                            {{ company_city }}
                                        </p>
                                        <p class="mb-0">
                                            {{ company_country }}
                                        </p>
                                    </div>
                                </div>
                                <div class="row align-content-center mt-5">
                                    <div class="col-md-7">
                                        <p class="mb-0">
                                            Remitter: {{ remitter_name }}
                                        </p>
                                        <p
                                            class="mb-0"
                                            v-if="transaction_type != 'BP'"
                                        >
                                            Beneficiary: {{ beneficiary_name }}
                                        </p>
                                        <p class="mb-0">
                                            Address: {{ remitter_address }}
                                        </p>
                                    </div>
                                    <div class="col-md-5 text-end">
                                        <p class="mb-0">
                                            Reference No.:
                                            <b>{{ reference_number }}</b>
                                        </p>
                                        <p class="mb-0">
                                            Contact No.:
                                            {{ remitter_contact_number }}
                                        </p>
                                        <p class="mb-0">
                                            Date:
                                            <b>{{
                                                $filters.dateFormat(
                                                    date_created
                                                )
                                            }}</b>
                                        </p>
                                    </div>
                                </div>
                                <div class="row align-content-center mt-5">
                                    <div class="col-md-7">
                                        <p class="mb-0 text-semi-bold">
                                            Transaction Details
                                        </p>
                                        <p class="mb-0">
                                            {{
                                                transaction_type == "BP"
                                                    ? "BP - BILLS PAYMENT"
                                                    : transaction_type == "DTD"
                                                    ? "DTD - DOOR TO DOOR"
                                                    : transaction_type == "CBA"
                                                    ? "CBA - CREDIT TO ACCOUNT"
                                                    : "OTC - CASH PICKUP ANYWHERE"
                                            }}
                                        </p>
                                        <p
                                            v-if="transaction_type == 'DTD'"
                                            class="mb-0"
                                        >
                                            Delivery Address:
                                            {{ delivery_address }}
                                        </p>
                                        <p
                                            v-if="transaction_type == 'CBA'"
                                            class="mb-0"
                                        >
                                            BANK NAME: {{ bank_name }}
                                        </p>
                                        <p
                                            v-if="transaction_type == 'CBA'"
                                            class="mb-0"
                                        >
                                            ACCOUNT NUMBER:
                                            {{ bank_account_number }}
                                        </p>
                                    </div>
                                    <div class="col-md-5 text-end">
                                        <p class="mb-0">
                                            Currency: {{ currency }}
                                        </p>
                                        <p class="mb-0">
                                            Equivalent Amount:
                                            {{ equivalent_amount }}
                                        </p>
                                        <p class="mb-0">
                                            Exchange Rate: {{ exchange_rate }}
                                        </p>
                                        <p class="mb-0">
                                            <b
                                                >Total Amount To Pay:
                                                {{ currency_to }}
                                                {{ gross_amount }}
                                            </b>
                                        </p>
                                        <p class="mb-0">
                                            Service Fee: {{ currency_to }}
                                            {{ service_fee }}
                                        </p>
                                        <p class="mb-0 text-15">
                                            Amount to Received:
                                            <b
                                                >{{ currency_to }}
                                                {{ net_amount }}</b
                                            >
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-md-12 terms">
                                        <p>
                                            IT IS DISTINCTLY UNDERSTOOD THAT THE
                                            ISSUING BANK SHALL ASSUME NO
                                            RESPONSIBILITY WHATSOEVER FOR THE
                                            DELAY IN THE EXECUTION OF THIS
                                            PAYMENT RESULTING FROM ERRORS AND/OR
                                            DELAY IN THE TRANSMISSION OF THE
                                            BANK'S RELATIVE INSTRUCTION TO IT'S
                                            PAYING BRANCH OFFICE OR
                                            CORRESPONDING OF FOR ANY CAUSE
                                            BEYOND ITS CONTROL ANV'S
                                            RESPONSIBILITY TO THE REMITTER SHALL
                                            COMMENCE ONLY UPON RECEIPT OF THE
                                            VALIDATED PAYMENT INSTRUCTIONS OR
                                            RNV FOREX OFFICIAL RECEIPT AND
                                            REMITTANCE OF FUND COVER FROM THE
                                            CONTRACT OR YOU CAN CANCEL FOR A
                                            FILL REFUND WITHIN 30 MINUTES OF
                                            PAYMENT UNLESS THE FUNDS HAVE BEEN
                                            PICKED UP OR DEPOSITED.
                                        </p>
                                        <p>
                                            PLEASE KEEP THIS RECEIPT IF YOU HAVE
                                            ANY COMPLAINT OR INQUIRY CONCERNING
                                            THIS REMITTANCE
                                        </p>
                                    </div>
                                </div>
                                <div class="row justify-content-between mt-5">
                                    <div class="col-md-5">
                                        <p class="consignee"></p>
                                        <p class="text-center text-semi-bold">
                                            {{
                                                user.first_name?.toUpperCase() +
                                                " " +
                                                user.middle_name?.toUpperCase() +
                                                " " +
                                                user.last_name?.toUpperCase()
                                            }}
                                        </p>
                                    </div>
                                    <div class="col-md-5">
                                        <p class="consignee"></p>
                                        <p class="text-center text-semi-bold">
                                            {{ remitter_name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        <p class="powered">Powered by</p>
                                        <img
                                            src="/images/allcash.png"
                                            class="img-fluid rounded-top allcash-logo"
                                            alt=""
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center my-5">
                                <div class="col-md-2">
                                    <button
                                        type="button"
                                        class="btn-cancel"
                                        @click="backPage"
                                    >
                                        CLOSE
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button
                                        type="button"
                                        class="btn-save"
                                        v-print="printObj"
                                    >
                                        PRINT
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "receipt",
    components: {},
    data() {
        return {
            user: this.$store.state.auth.user,

            inbountToken: "",

            company_logo: "",
            company_address: "",
            company_city: "",
            company_country: "",

            beneficiary_name: "",

            remitter_name: "",
            remitter_address: "",
            remitter_contact_number: "",
            reference_number: "",
            transaction_type: "",

            date_created: "",
            delivery_address: "",
            bank_name: "",
            bank_account_number: "",

            currency: "",
            currency_from: "",
            currency_to: "",
            equivalent_amount: "",
            exchange_rate: "",
            gross_amount: "",
            service_fee: "",
            net_amount: "",

            printObj: {
                id: "printMe",
                popTitle: "",
                extraCss: "http://127.0.0.1:4000/resources/css/print.css",
                extraHead:
                    '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">',
                beforeOpenCallback(vue) {
                    console.log("beforeOpenCallback");
                },
                openCallback(vue) {
                    console.log("openCallback");
                },
                closeCallback(vue) {
                    console.log("closeCallback");
                },
            },
        };
    },
    methods: {
        backPage() {
            this.$router.push("/app/transaction/data-entry");
        },
    },
    created() {
        axios
            .get(`/api/transactions/${this.$route.query.transactionID}`)
            .then(async (data) => {
                axios
                    .get("/api/get-inbound-api-token")
                    .then((data) => {
                        this.inbountToken = data.data.token;
                    })
                    .finally(async (e) => {
                        await axios
                            .get(
                                this.$eosUrl +
                                    "/api/outbound/get-branch-details",
                                {
                                    params: {
                                        "branch-id": this.user.branch_id,
                                    },
                                    headers: {
                                        Authorization:
                                            "Bearer " + this.inbountToken,
                                    },
                                }
                            )
                            .then((res) => {
                                this.company_logo =
                                    this.$eosUrl +
                                    "/images/" +
                                    res.data.data.logo;
                                (this.company_address = res.data.data.country),
                                    (this.company_country =
                                        res.data.data.address);
                                this.company_city = res.data.data.city;
                                // this.currency = res.data.data.currency;
                                // this.currency_name =
                                //     res.data.data.currency_name;
                                // this.equivalent_amount =
                                //     res.data.data.equivalent_amount;
                            })
                            .catch((e) => {
                                errorMessage("Opps!", e.message, "top-right");
                            });

                        console.log("receipt");
                        this.currency_from =
                            data.data.data.currency.split("-")[0];
                        this.currency_to =
                            data.data.data.currency.split("-")[1];
                        this.transaction_type = data.data.data.type;
                        this.date_created = data.data.data.date_created;

                        this.remitter_name = data.data.data.remitter_name;
                        this.reference_number = data.data.data.reference_number;
                        this.remitter_contact_number =
                            data.data.data.remitter_contact_number;
                        this.remitter_address = data.data.data.remitter_address;

                        this.beneficiary_name = data.data.data.beneficiary_name;

                        this.currency = data.data.data.currency;
                        this.currency_name = data.data.data.currency_name;
                        this.equivalent_amount =
                            this.currency_from +
                            " " +
                            data.data.data.equivalent_amount;
                        this.exchange_rate = data.data.data.exchange_rate;
                        this.gross_amount = data.data.data.gross_amount;
                        this.service_fee = data.data.data.service_fee;
                        this.net_amount = data.data.data.net_amount;

                        this.bank_name = data.data.data.bank_name;
                        this.bank_account_number =
                            data.data.data.bank_account_number;

                        this.delivery_address = data.data.data.delivery_address;
                    });
            });
    },
};
</script>
