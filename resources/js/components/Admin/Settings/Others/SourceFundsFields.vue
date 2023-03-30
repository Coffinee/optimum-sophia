<template>
    <div class="col-md-12">
        <div class="card card-child shadow mb-4">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <h6 class="m-0 text-primary text-white">
                    SOURCE OF FUNDS CUSTOM FIELDS
                </h6>
                <a @click.prevent="addField" href="#" class="btn-tab">
                    <i class="bi bi-plus-circle"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="px-2 pt-4">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%">NAME</th>
                                <th class="text-center">CUSTOM FIELDS</th>
                                <th class="text-center" style="width: 10%">
                                    ACTIONS
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <TableLoader
                                :showLoader="isLoading"
                                :colLength="2"
                            ></TableLoader>
                            <tr v-if="showNoFound">
                                <td colspan="3">
                                    <img
                                        class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img"
                                        src="/images/no-data.png"
                                        alt=""
                                    />
                                </td>
                            </tr>
                            <tr
                                v-for="(item, itemIndex) in customFields.data"
                                :key="itemIndex"
                            >
                                <td>{{ item.fund_name.name }}</td>
                                <td class="text-center">
                                    <a
                                        href="#"
                                        @dblclick="hideField(itemIndex)"
                                        @click.prevent="toggleField(itemIndex)"
                                        >{{
                                            itemIndex == itemToShow
                                                ? "HIDE"
                                                : "SHOW"
                                        }}
                                        CUSTOM FIELDS
                                    </a>
                                    <table
                                        v-show="itemIndex == itemToShow"
                                        class="table table-sm animate__animated animate__fadeIn"
                                    >
                                        <thead class="bg-light-black">
                                            <tr class="text-center">
                                                <td style="width: 33%">TYPE</td>
                                                <td style="width: 33%">
                                                    LABEL
                                                </td>
                                                <td style="width: 33%">
                                                    is REQUIRED?
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    val, index
                                                ) in JSON.parse(item.fields)"
                                                :key="index"
                                            >
                                                <td>{{ val.type }}</td>
                                                <td>{{ val.label }}</td>
                                                <td class="text-center">
                                                    {{ val.require }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- <p class="m-0" v-for="(val,index) in JSON.parse(item.fields)" :key="index">
                                        Type: {{val.type}}, Label: {{val.label}}, Required: {{val.require}}, {{val.type == 'select' ? 'Options:'+ val.options.map(el =>  el.text ).join(', ') : ''}} 
                                    </p> -->
                                </td>
                                <td
                                    class="text-center d-flex justify-content-center"
                                >
                                    <a
                                        @click.prevent="editField(item)"
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
            <!-- <div class="card-footer">
                    <LaravelVuePagination class="float-right" :data="fundsData" @pagination-change-page="paginatedFunds"></LaravelVuePagination>
                </div> -->
        </div>
    </div>
    <RightSideModal
        icon="bi bi-currency-exchange"
        :widthModal="'45%'"
        :heightModal="'100vh'"
    >
        <template v-slot:header
            >{{ !editmode ? "ADD " : "UPDATE" }} CUSTOM FIELDS</template
        >
        <template v-slot:body>
            <form @submit.prevent="!editmode ? saveFields() : updateFields()">
                <div class="row justify-content-center py-4">
                    <div class="col-md-11 mb-3">
                        <div class="form-floating vs">
                            <v-select
                                v-model="formFields.source_of_funds"
                                :options="sourceOfFunds"
                                :class="{
                                    'is-invalid':
                                        formFields.errors.has(
                                            'source_of_funds'
                                        ),
                                }"
                                placeholder="Select source of fund"
                                id="branch"
                            />
                            <label for="branch"
                                >SOURCE OF FUNDS <span>*</span></label
                            >
                        </div>
                        <HasError :form="formFields" field="source_of_funds" />
                    </div>
                    <div class="col-md-10">
                        <div class="form-floating vs">
                            <v-select
                                v-model="inputType"
                                :options="inputTypes"
                                :class="{
                                    'is-invalid':
                                        formFields.errors.has('fields'),
                                }"
                                placeholder="Select input type"
                                id="input-type"
                            />
                            <label for="input-type"
                                >INPUT TYPE <span>*</span></label
                            >
                        </div>
                        <HasError :form="formFields" field="fields" />
                    </div>
                    <div class="col-md-1 px-1">
                        <button
                            type="button"
                            @click="addRow($event), (typeCounter += 1)"
                            class="btn-modal-flex-add"
                        >
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                <div
                    class="row justify-content-center py-2 g-1"
                    v-for="(itemData, index) in formFields.fields"
                    :key="index"
                >
                    <div
                        :class="[
                            itemData.type != 'select' ? 'col-md-7' : 'col-md-4',
                        ]"
                    >
                        <FloatingInput
                            v-model="itemData.label"
                            type="text"
                            :label="
                                'Enter label for ' + itemData.type + ' type'
                            "
                            name="type"
                            :isInvalid="
                                formFields.errors.has(
                                    'fields.' + index + '.label'
                                )
                            "
                            :required="true"
                            placeholder=""
                        ></FloatingInput>
                        <HasError
                            :form="formFields"
                            :field="'fields.' + index + '.label'"
                        />
                    </div>
                    <div class="col-md-4" v-if="itemData.type == 'select'">
                        <vue-tags-input
                            v-show="itemData.type == 'select'"
                            v-model="itemData.tag"
                            :tags="itemData.options"
                            @tags-changed="
                                (newTags) => (itemData.options = newTags)
                            "
                        />
                    </div>
                    <div
                        :class="[
                            itemData.type == 'select' ? 'col-md-2' : 'col-md-3',
                        ]"
                    >
                        <div class="form-floating vs">
                            <v-select :options="require" id="input-type" />
                            <label for="input-type"
                                >REQUIRED? <span>*</span></label
                            >
                        </div>
                    </div>
                    <div class="col-md-1 px-1">
                        <button
                            type="button"
                            @click.prevent="removeRow(itemData, $event)"
                            class="btn-modal-flex-minus"
                        >
                            <i class="bi bi-dash-lg"></i>
                        </button>
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
                            CANCEL
                        </button>
                    </div>
                    <div class="col">
                        <button
                            :disabled="formFields.busy"
                            type="submit"
                            class="btn-save"
                        >
                            {{ editmode ? "UPDATE" : "SAVE" }}
                        </button>
                    </div>
                </div>
            </form>
        </template>
    </RightSideModal>
</template>

<script>
import Form from "vform";
import { HasError } from "vform/src/components/bootstrap5";
import { errorMessage, successMessage } from "@/utilities.js";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
import FloatingInput from "@/components/Misc/Inputs/FloatingInput.vue";
import LaravelVuePagination from "laravel-vue-pagination";
import RightSideModal from "@/components/Misc/Modals/RightSideModal.vue";
import VueTagsInput from "@sipec/vue3-tags-input";
export default {
    name: "source-of-funds-fields",
    components: {
        FloatingInput,
        TableLoader,
        HasError,
        LaravelVuePagination,
        RightSideModal,
        VueTagsInput,
    },
    data() {
        return {
            editmode: false,
            isLoading: true,
            customFields: {},
            sourceOfFunds: [],
            showNoFound: false,
            typeCounter: 1,
            itemToShow: -1,
            showCustomFields: true,
            inputType: { label: "Text", value: "text" },
            require: [
                { label: "True", value: true },
                { label: "False", value: false },
            ],
            inputTypes: [
                { label: "Text", value: "text" },
                { label: "Date", value: "date" },
                { label: "Number", value: "number" },
                { label: "File", value: "file" },
                { label: "Image", value: "image" },
                { label: "Email", value: "email" },
                { label: "Select", value: "select" },
            ],
            formFields: new Form({
                id: "",
                source_of_funds: "",
                code: "source-of-fund",
                fields: [],
            }),
        };
    },
    methods: {
        addField() {
            this.editmode = false;
            this.formFields.reset();
            jQuery("#modal-right-side").modal("show");
        },
        closeModal() {
            jQuery("#modal-right-side").modal("hide");
        },
        addRow() {
            this.formFields.fields.push({
                type: this.inputType.value,
                label: "",
                require: true,
            });
        },
        removeRow(itemData, event) {
            event.preventDefault();
            this.formFields.fields.splice(
                this.formFields.fields.indexOf(itemData),
                1
            );
        },
        saveFields() {
            this.$Progress.start();
            this.formFields
                .post("/api/custom-fields")
                .then(() => {
                    successMessage(
                        "Added!",
                        "Custom fields has been added.",
                        "top-left"
                    );
                    this.formFields.reset();
                    this.$Progress.finish();
                    this.loadData();
                    setTimeout(() => {
                        jQuery("#modal-right-side").modal("hide");
                    }, 1500);
                })
                .catch((e) => {
                    this.$Progress.fail();
                    errorMessage(
                        "Opps!",
                        "Something went wrong in adding a custom fields.",
                        "top-right"
                    );
                });
        },
        toggleField(index) {
            this.itemToShow = index;
        },
        hideField(index) {
            this.itemToShow = -1;
        },
        editField() {},
        updateFields() {},
        loadData() {
            axios
                .get("/api/custom-fields", {
                    params: {
                        code: "source-of-fund",
                    },
                })
                .then((data) => {
                    this.showNoFound = data.data.data.total == 0 ? true : false;
                    this.customFields = data.data.data;
                    this.isLoading = false;
                })
                .catch(() => {
                    console.log("err");
                });
        },
    },
    created() {
        this.loadData();
        axios
            .get("/api/get-source-of-funds")
            .then(({ data }) => (this.sourceOfFunds = data.data));
    },
};
</script>
