<template>
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 text-right text-white">COMPANY</h6>
                </div>
                <div class="card-body">
                    <div class="row g-1 justify-content-between">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="form-group has-search">
                                        <i
                                            class="bi bi-search form-control-feedback"
                                        ></i>
                                        <input
                                            v-model="search"
                                            type="text"
                                            v-debounce:500ms="searchCompany"
                                            name="search"
                                            class="form-control"
                                            placeholder="Search"
                                        />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <ButtonBlack
                                        icon="bi bi-download"
                                        label="Export"
                                    ></ButtonBlack>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <ButtonOrange
                                icon="bi bi-plus-circle"
                                label="Add Company"
                                @click="openModal"
                            ></ButtonOrange>
                        </div>
                    </div>
                    <div class="col-md-12 py-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" /></th>
                                    <th scope="col">COMPANY ID</th>
                                    <th scope="col">COMPANY NAME</th>
                                    <th scope="col">PARTNER CODE</th>
                                    <th scope="col">DATE ADDED</th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader
                                    :showLoader="isLoading"
                                    :colLength="4"
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
                                <tr
                                    v-for="item in companies.data"
                                    :key="item.id"
                                >
                                    <td>
                                        <input type="checkbox" />
                                    </td>
                                    <td scope="row">{{ item.id }}</td>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.code }}</td>
                                    <td>
                                        {{
                                            $filters.dateFormat(item.created_at)
                                        }}
                                    </td>
                                    <td
                                        class="text-center d-flex justify-content-evenly align-content-center"
                                    >
                                        <a
                                            @click.prevent="editCompany(item)"
                                            class="rounded-s-button"
                                            href="#"
                                        >
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input table-button"
                                                type="checkbox"
                                                id="flexSwitchCheckChecked"
                                                @change.prevent="
                                                    setCompanyStatus(
                                                        item.id,
                                                        item.is_active
                                                    )
                                                "
                                                :checked="item.is_active"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <LaravelVuePagination
                        class="float-right"
                        :data="companies"
                        @pagination-change-page="paginatedCompanies"
                    ></LaravelVuePagination>
                </div>
            </div>
        </div>
        <RightSideModal
            icon="bi bi-building"
            :widthModal="'35%'"
            :heightModal="'100vh'"
        >
            <template v-slot:header
                >{{ !editmode ? "ADD COMPANY" : this.form.name }}
            </template>
            <template v-slot:body>
                <form
                    @submit.prevent="
                        !editmode ? saveCompany() : updateCompany()
                    "
                >
                    <div class="row justify-content-center">
                        <div class="py-2 text-center">
                            <div
                                class="company-logo"
                                :style="{
                                    'background-image': `url(${previewImage})`,
                                }"
                            ></div>
                            <input
                                ref="fileCompanyLogo"
                                class="inputfile my-2"
                                type="file"
                                @input="pickFile"
                            />
                            <label
                                for="file"
                                @click="$refs.fileCompanyLogo.click()"
                            >
                                UPLOAD</label
                            >
                        </div>
                        <div class="col-md-11 pt-2">
                            <div class="custom-control custom-switch text-end">
                                <input
                                    v-model="form.is_active"
                                    :checked="form.is_active"
                                    type="checkbox"
                                    class="custom-control-input"
                                    id="customSwitch1"
                                    :value="form.is_active"
                                />
                                <label
                                    class="custom-control-label"
                                    for="customSwitch1"
                                    >Active</label
                                >
                            </div>
                            <GroupHalfInput
                                v-model="form.name"
                                label="COMPANY NAME"
                                type="text"
                                name="company-name"
                                :isInvalid="form.errors.has('name')"
                            ></GroupHalfInput>
                            <HasError
                                :form="form"
                                field="name"
                                class="text-end"
                            />
                            <GroupHalfInput
                                v-model="form.code"
                                label="PARTNER CODE"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                                :isInvalid="form.errors.has('code')"
                            ></GroupHalfInput>
                            <HasError
                                :form="form"
                                field="code"
                                class="text-end"
                            />
                        </div>
                    </div>
                    <div class="modal-button-holder justify-content-around">
                        <div class="col">
                            <button
                                data-dismiss="modal"
                                type="button"
                                @click="closeModal"
                                class="btn-cancel"
                            >
                                Cancel
                            </button>
                        </div>
                        <div class="col">
                            <button
                                :disabled="form.busy"
                                type="submit"
                                class="btn-save"
                            >
                                {{ editmode ? "Update" : "Save" }} changes
                            </button>
                        </div>
                    </div>
                </form>
            </template>
        </RightSideModal>
    </div>
</template>

<script>
// import { $array } from "alga-js";
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
    name: "company",
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
            editmode: false,
            isModalVisible: false,
            showModalNow: false,
            showNoFound: false,
            user: this.$store.state.auth.user,
            hasPagination: false,
            companies: {},
            search: "",
            isLoading: true,
            form: new Form({
                id: "",
                is_active: true,
                name: "",
                code: "",
                logo: "",
            }),
            previewImage: "/images/upload-icon.png",
        };
    },
    methods: {
        openModal() {
            this.editmode = false;
            this.form.reset();
            this.previewImage = "/images/upload-icon.png";
            this.form.code = Math.random()
                .toString(36)
                .slice(2, 10)
                .toUpperCase();
            jQuery("#modal-right-side").modal("show");
        },
        closeModal() {
            jQuery("#modal-right-side").modal("hide");
        },
        chooseFile() {
            this.pickFile();
        },
        pickFile() {
            let input = this.$refs.fileCompanyLogo;
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
        saveCompany() {
            this.$Progress.start();
            this.form
                .post("/api/companies")
                .then(() => {
                    this.previewImage = "/images/upload-icon.png";
                    successMessage(
                        "Added!",
                        this.form.name + " has been added.",
                        "top-left"
                    );
                    this.form.reset();
                    this.$Progress.finish();
                    this.loadData();
                    setTimeout(() => {
                        jQuery("#modal-right-side").modal("hide");
                    }, 1500);
                })
                .catch(() => {
                    this.$Progress.fail();
                    errorMessage(
                        "Opps!",
                        "Something went wrong in adding a company.",
                        "top-right"
                    );
                });
        },
        editCompany(item) {
            this.editmode = true;
            this.form.reset();
            this.previewImage =
                item.logo != null
                    ? "/uploads/200x200/" + item.logo
                    : "/images/upload-icon.png";
            this.form.fill(item);
            jQuery("#modal-right-side").modal("show");
        },
        updateCompany() {
            this.$Progress.start();
            this.form
                .put("/api/companies/" + this.form.id)
                .then((res) => {
                    successMessage(
                        "Added!",
                        this.form.name + " has been updated.",
                        "top-left"
                    );
                    this.$Progress.finish();
                    this.form.reset();
                    this.loadData();
                    this.editmode = false;
                    setTimeout(() => {
                        jQuery("#modal-right-side").modal("hide");
                    }, 1500);
                })
                .catch(() => {
                    this.$Progress.fail();
                })
                .finally(() => {});
        },
        setCompanyStatus(id, status) {
            this.$Progress.start();
            axios
                .put("/api/change-company-status", {
                    params: {
                        id: id,
                        status: status,
                    },
                })
                .then((data) => {
                    this.loadData();
                    successMessage("Hello!", data.data.message, "top-right");
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
        },
        paginatedCompanies(page = 1) {
            axios.get("/api/companies?page=" + page).then((response) => {
                this.companies = response.data.data;
                this.hasPagination = true;
            });
        },
        searchCompany() {
            const query = this.search;
            axios
                .get("/api/company-search?q=" + query)
                .then((response) => {
                    this.companies = response.data.data;
                })
                .catch(() => {});
        },
        loadData() {
            let config = {
                headers: {
                    Authorization:
                        "Bearer 3|3HztMssBhdspaHQDLhyW0iGAFmEyMb9ym1VNDrf8",
                },
            };
            axios
                .get(this.$eosUrl + "/api/outbound/companies", {}, config)
                .then((data) => {
                    this.isLoading = false;
                    this.showNoFound = data.data.data.total == 0 ? true : false;
                    this.companies = data.data.data;
                })
                .catch(() => {
                    console.log("err");
                });
        },
    },
    computed: {},
    created() {
        this.loadData();
        console.log(this.$eosUrl);
    },
};
</script>
