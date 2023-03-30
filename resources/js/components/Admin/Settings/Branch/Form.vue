<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        
            <div class="col-lg-12 mb-4">
                <form @submit.prevent="!editMode ? saveBranch() : updateBranch()">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-right text-white">
                            CREATE BRANCH
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="row p-3 justify-content-between">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="company-logo" :style="{ 'background-image': `url(${previewImage})`, }" ></div>
                                    <input ref="fileBranchPix" class="inputfile my-2" type="file" @input="pickFile"/>
                                    <label for="file" @click="$refs.fileBranchPix.click()"><i class="bi bi-upload mr-2"></i> UPLOAD</label>
                                </div>
                                <HasError :form="form" field="branch_logo" class="text-end" />
                            </div>

                            <div class="col-md-4 ml-auto mb-3">
                                <GroupHalfDropdown v-model="form.status" label="STATUS" :options="statusOptions" :selected="form.status" name="company-name"></GroupHalfDropdown>
                                <GroupHalfInput v-model="form.code" label="PARTNER CODE" type="text" name="company-code" :isDisabled="true"></GroupHalfInput>
                                <HasError :form="form" field="code" class="text-end" />
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-12 mb-3">
                            <h4 class="form-banner">COMPANY DETAILS</h4>
                        </div>
                    </div>
                    <div class="row px-3">
                        <div class="col-md-4 mb-3">
                            <div class="form-floating vs">
                                <v-select v-model="form.company"  :class="{'is-invalid': form.errors.has('company'), }" :options="allCompanies" placeholder="SELECT COMPANY" id="company"/>
                                <label for="company" >Company <span>*</span></label>
                            </div>
                            <HasError :form="form" field="company" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h4 class="form-banner">BRANCH DETAILS</h4>
                        </div>
                    </div>
                    <div class="row px-3">
                        <div class="col-md-6">
                            <div class="row g-1">
                                <div class="col-8 mb-3">
                                    <FloatingInput v-model="form.name" label="Branch Name" name="address" :isInvalid="form.errors.has('name')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="name" />
                                </div>
                                <div class="col-4 mb-3">
                                    <FloatingInput v-model="form.id" label="Branch ID" :disabled="true"  name="address" :isInvalid="form.errors.has('id')" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="id" />
                                </div>
                            </div>
                            <div class="row g-1">
                                <div class="col-12 mb-3">
                                    <FloatingInput v-model="form.address" label="Address" name="address" :isInvalid="form.errors.has('address')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="address" />
                                </div>
                                <div class="col-sm-12 col-md-6 mb-3">
                                    <div class="form-floating vs">
                                        <v-select v-model="form.country" @option:selected="getStatesByCountry" :class="{'is-invalid':form.errors.has('country'),}" :options="countries" placeholder="COUNTRY" id="company"/>
                                        <label for="country">Country <span>*</span></label>
                                    </div>
                                    <HasError :form="form" field="country" />
                                </div>
                                <div class="col-sm-12 col-md-6 mb-3" v-if="isState">
                                    <div class="form-floating vs">
                                        <v-select v-model="form.state" @option:selected="getCityByState" :class="{ 'is-invalid': form.errors.has('state'), }" :options="states" placeholder="STATE" id="state"/>
                                        <label for="state">State <span>*</span></label>
                                    </div>
                                    <HasError :form="form" field="state" />
                                </div>
                                <div class="col-sm-12 col-md-6 mb-3"  v-if="!isState">
                                    <div class="form-floating vs">
                                        <v-select v-model="form.region" @option:selected="getCityByState" :class="{ 'is-invalid': form.errors.has('state'), }" :options="regions" placeholder="Region" id="state"/>
                                        <label for="region">Region <span>*</span></label>
                                    </div>
                                    <HasError :form="form" field="region" />
                                </div>
                                <div class="col-sm-12 mb-3" :class="[isState ? 'col-md-6' : 'col-md-5' ]" v-if="!isState">
                                    <div class="form-floating vs">
                                        <v-select v-model="form.province"  :class="{ 'is-invalid': form.errors.has('province'), }" :options="provinces" placeholder="Province" id="province"/>
                                        <label for="province">Province <span>*</span></label>
                                    </div>
                                    <HasError :form="form" field="province" />
                                </div>
                                <div class="col-sm-12  mb-3" :class="[isState ? 'col-md-6' : 'col-md-5' ]">
                                    <div class="form-floating vs">
                                        <v-select v-model="form.city" :loading="fetchingCities" :class="{'is-invalid': form.errors.has('city'),}" :options="cities" placeholder="CITY" id="company"/>
                                        <label for="city">City <span>*</span></label>
                                    </div>
                                    <HasError :form="form" field="city" />
                                </div>
                                <div class="col-sm-12 mb-3" :class="[isState ? 'col-md-6' : 'col-md-2' ]">
                                    <FloatingInput v-model="form.zip_code" label="Zip Code" name="zip_code" :isInvalid="form.errors.has('zip_code')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="zip_code" />
                                </div>
                                <div class="col-sm-12 col-md-6 mb-3">
                                    <FloatingInput v-model="prefix_preview" @input="generateRefNumber" label="Prefix for Reference Number" name="zip_code" :isInvalid="form.errors.has('prefix')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="prefix" />
                                </div>
                                <div class="col-sm-12 col-md-6 mb-3">
                                    <FloatingInput v-model="form.prefix" label="Preview Rerefence Number" name="prefix" :disabled="true" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="prefix" />
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-1 mb-2">
                                <div class="col-10 sub-form-banner">
                                    <h5 class="m-0">CURRENCIES</h5>
                                </div>
                                <div class="col-2 text-end">
                                    <button @click="addRow($event),counter += 1" type="button" class="btn-card-add"> <i class="bi bi-plus-lg"></i></button>
                                </div>
                                <div class="col-12">
                                    <span><HasError :form="form" field="currencies" /></span>
                                </div>
                            </div>
                            <div class="row g-1" v-for="(itemData, index) in form.currencies" :key="index">
                                <div class="col-5 mb-3">
                                    <div class="form-floating vs">
                                        <v-select v-model="itemData.currency_from" :options="currencies" :class="{ 'is-invalid': form.errors.has('currency_from'), }" id="currency_from"/>
                                        <label for="currency_from">From <span>*</span></label>
                                    </div>
                                </div>
                                <div class="col-5 mb-3">
                                    <div class="form-floating vs">
                                        <v-select v-model="itemData.currency_to" :options="currencies" :class="{ 'is-invalid': form.errors.has('currency_to'), }"  id="currency_to"/>
                                        <label for="currency_to" >To <span>*</span></label >
                                    </div>
                                </div>
                                <div class="col-2 mb-3 text-end">
                                    <button type="button" @click.prevent="removeRow(itemData,$event)" class="btn-card-minus"> <i class="bi bi-dash-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row g-1 justify-content-center mt-3 mb-5">
                        <div class="col-md-2">
                            <button type="button" @click="backToBranchPage" class="btn-cancel">Cancel</button>
                        </div>
                        <div class="col-md-2">
                            <button :disabled="form.busy" type="submit" class="btn-save">{{ !editMode ? "SAVE" : "UPDATE"}} </button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        
    </div>
</template>

<script>
import Form from "vform";
import { Button, HasError, AlertError } from "vform/src/components/bootstrap5";

import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import GroupHalfInput from "@/components/Misc/Inputs/GroupHalfInput.vue";
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import DropdownWithSearch from "@/components/Misc/Inputs/DropdownWithSearch.vue";
import GroupHalfDropdown from "@/components/Misc/Inputs/GroupHalfDropdown.vue";
import FloatingInput from "@/components/Misc/Inputs/FloatingInput.vue";

import { Country, State, City } from "country-state-city";

export default {
    name: "create-branch",
    components: {
        // vSelect,
        ButtonBlack,
        ButtonOrange,
        GroupHalfInput,
        FloatingInput,
        HasError,
        AlertError,
        DropdownWithSearch,
        GroupHalfDropdown,
    },
    data() {
        return {
            previewImage: "/images/upload-icon.png",
            user: this.$store.state.auth.user,
            editMode: false,
            statusOptions: {
                0: "Inactive",
                1: "Active",
            },
            counter:1,
            fetchingCities:false,
            allCompanies: [],
            countries: [],
            cities: [],
            states: [],
            regions:[],
            provinces:[],
            zip_code: [],
            currencies: [],
            isState:true,
            prefix_preview:'',
            form: new Form({
                id:'',
                status:'1',
                code:'',
                company: '',
                prefix:'',
                name: "",
                address: "",
                country: "",
                state: "",
                region:'',
                province:'',
                city: "",
                zip_code: "",
                currencies: [
                    {
                        id:'',
                        currency_from:'',
                        currency_to:''
                    }
                ],
                logo: "",
                user_id: this.$store.state.auth.user.id
            }),
        };
    },
    methods: {
        pickFile() {
            let input = this.$refs.fileBranchPix;
            let file = input.files;
            if (file && file[0]) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                    this.form.logo = e.target.result;
                };
                reader.readAsDataURL(file[0]);
                this.$emit("input", file[0]);
            }
        },
        backToBranchPage() {
            this.$router.push("/app/settings/branch");
        },
        addRow(event){
            event.preventDefault()
            this.form.currencies.push({
                id:'',
                currency_from:'',
                currency_to:'',
            });
        },
        removeRow(itemData,event){
            event.preventDefault()
            this.counter--
            this.form.currencies.splice(this.form.currencies.indexOf(itemData),1);
        },
        generateRefNumber(){
           this.form.prefix = this.prefix_preview == "" ? "" : this.prefix_preview + this.$moment().endOf("day").format('MMDDYYYY')+'000001'
        },
        saveBranch() {
            this.$Progress.start();
            this.form.post("/api/branches")
                .then(() => {
                    this.$moshaToast(
                        this.form.name + " branch has been created",
                        {
                            showIcon: true,
                            hideProgressBar: true,
                            timeout: 2000,
                            transition: "slide",
                            type: "success",
                            position: "top-right",
                        }
                    );
                    this.form.reset();
                    this.previewImage = "/images/upload-icon.png";
                    this.$Progress.finish();
                    this.backToBranchPage();
                })
                .catch((err) => {
                    this.$Progress.fail();
                    console.log(err);
                    this.$moshaToast(
                        {
                            title: "Opps!",
                            description:
                                "Something went wrong in creating the branch.",
                        },
                        {
                            type: "danger",
                            showIcon: true,
                            hideProgressBar: true,
                            position: "top-right",
                        }
                    );
                });
        },
        updateBranch() {
            this.$Progress.start();
            this.form.reference_no =
                this.ref_prefix + this.ref_date + this.ref_no_of_digits;
            this.form.currency =
                this.currency_from + "-" + this.currency_to.label;
            this.form.country = this.form.country.label;
            this.form.state = this.form.state.label;
            this.form.city = this.form.city.label;

            // console.log("update", this.currency_to, this.currency_from, this.form);

            this.form
                .put("/api/branches/" + this.form.id)
                .then((res) => {
                    console.log("logo", this.form.logo);
                    this.$moshaToast(
                        "Branch " + this.form.name + " has been updated.",
                        {
                            showIcon: true,
                            hideProgressBar: true,
                            timeout: 2000,
                            transition: "slide",
                            type: "success",
                            position: "top-left",
                        }
                    );
                    this.resetForm();
                    this.form.reset();
                    this.previewImage = "/images/upload-icon.png";
                    this.editmode = false;
                    this.$Progress.finish();

                    setTimeout(() => {
                        this.backToBranchPage();
                    }, 1500);
                })
                .catch((err) => {
                    this.$Progress.fail();
                    console.log(err.message);
                })
                .finally(() => {});
        },
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
        getStatesByCountry(data) {
            axios
                .get(
                    "/api/address/countries/" + data.value +"/states"
                )
                .then((data) => {
                    this.isState = data.data.data.hasState
                     if(this.isState){
                        this.states = data.data.data.data;
                    }
                    else{
                        this.regions = data.data.data.data;
                        this.provinces = data.data.data.provinces
                    }
                })
                .catch((err) => {
                    console.log(err);
                });

        },
        getCityByState(data){
            this.fetchingCities = true
            axios.get("/api/address/states/" + data.value +"/cities")
                .then((data) => {
                    this.cities = data.data.data
                    this.fetchingCities = false
                })
                .catch((err) => {
                    console.log(err);
                });
        },  
        getAllCurrencies() {
            axios.get("/api/address/currencies")
                .then((data) => {
                    this.currencies = data.data.data;
                })
                .catch((err) => {
                    console.log(err);
                });
        },


    },
    computed: {},
    created() {
        axios.get("/api/get-companies").then(({ data }) => (this.allCompanies = data.data));
        this.getAllCountries();
        this.getAllCurrencies();
        this.form.code = Math.random().toString(36).slice(2, 10).toUpperCase();

    },
};
</script>
