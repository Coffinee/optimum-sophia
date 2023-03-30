<template>
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">
                        BATCH UPLOAD
                    </h6>
                </div>
                <form
                    @submit.prevent="saveTransaction()"
                    @keydown="form.onKeydown($event)"
                    enctype="multipart/form-data"
                >
                    <div class="card-body pad-70">
                            <div class="d-flex justify-content-between b-dashed">
                            <div class="row">
                                <div class="col-md-6 offset-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="company-logo" :style="{ 'background-image': `url(${previewImage})`, }"
                                        ></div>
                                        <input
                                            ref="transactionFile"
                                            class="inputfile my-2"
                                            type="file"
                                            accept=".txt,.xlsx,.xls,.csv"
                                            @input="pickFile"
                                            @change="handleFile"
                                        />
                                    </div>
                                    <HasError
                                        :form="formUpload.file"
                                        field="profile_picture"
                                        class="text-end"
                                    />
                                </div>

                                <div class="text-center mt-3 mb-3">
                                    <p>DRAG AND DROP FILE OR</p>
                                </div>

                                <div class="col-md-4 offset-md-4 text-center">
                                    <label
                                        :disabled="form.busy"
                                        @click="$refs.transactionFile.click()"
                                        class="btn-save"
                                    >
                                        <i class="bi bi-upload mr-2"></i>
                                        BROWSE
                                    </label>
                                </div>
                            </div>
                            <div class="vr"></div>
                            <div class="row row-cols-1">
                                <div
                                    class="col-lg-12"
                                    v-if="this.file_size_total != 0"
                                >
                                    <div class="col-lg-10 offset-lg-2 mb-3">
                                        <span
                                            ><i class="bi bi-check-circle"></i>
                                            &nbsp;</span
                                        >
                                        <u>{{ this.formUpload.file.name }} </u>

                                        <div class="progress">
                                            <!-- v-model the width in style and text -->
                                            <div
                                                class="progress-bar progress-bar bg-success"
                                                role="progressbar"
                                                aria-label="Success striped example"
                                                :style="{
                                                    width:
                                                        formUpload.progress +
                                                        '%',
                                                }"
                                                aria-valuenow="0"
                                                aria-valuemin="0"
                                                aria-valuemax="100"
                                            >
                                                {{ formUpload.progress }}
                                            </div>
                                        </div>
                                        <div
                                            class="row justify-content-between"
                                        >
                                            <p style="size: 10px">
                                                {{
                                                    this.file_size_progress.toFixed(
                                                        2
                                                    ) +
                                                    this.file_size_conversion +
                                                    " of "
                                                }}
                                                {{
                                                    this.file_size_total.toFixed(
                                                        2
                                                    ) +
                                                    this.file_size_conversion
                                                }}
                                            </p>

                                            <div>
                                                {{
                                                    formUpload.progress !=
                                                    undefined
                                                        ? "Uploading " +
                                                          formUpload.progress +
                                                          "%"
                                                        : ""
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="col-lg-5 offset-lg-4 mb-3">
                                        <FloatingInput
                                            v-model="form.total_count"
                                            label="Item Count"
                                            name="item-count"
                                            :disabled="formUpload.disabled"
                                            :isInvalid="
                                                form.errors.has('item-count')
                                            "
                                            :required="true"
                                            placeholder=""
                                        ></FloatingInput>
                                        <HasError
                                            :form="form"
                                            field="item-count"
                                        />
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="col-lg-5 offset-lg-4 mb-3">
                                        <FloatingInput
                                            v-model="form.total_amount"
                                            label="Total Amount"
                                            name="total_amount"
                                            :disabled="formUpload.disabled"
                                            :isInvalid="
                                                form.errors.has('total_amount')
                                            "
                                            :required="true"
                                            placeholder=""
                                        ></FloatingInput>
                                        <HasError
                                            :form="form"
                                            field="total_amount"
                                        />
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="col-lg-5 offset-lg-4 mb-3">
                                        <FloatingInput
                                            v-model="form.exchange_rate"
                                            label="Exchange Rate"
                                            name="exchange_rate"
                                            :disabled="formUpload.disabled"
                                            :isInvalid="
                                                form.errors.has('exchange_rate')
                                            "
                                            :required="true"
                                            placeholder=""
                                        ></FloatingInput>
                                        <HasError
                                            :form="form"
                                            field="exchange_rate"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
          

                        <div class="row g-1 justify-content-center mt-5 mb-3">
                            <div class="col-md-2">
                                <button
                                    type="button"
                                    @click="resetForm"
                                    class="btn-cancel"
                                >
                                    Reset
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn-save">
                                    SUBMIT
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Form from "vform";
import { Button, HasError, AlertError } from "vform/src/components/bootstrap5";
import FloatingInput from "@/components/Misc/Inputs/FloatingInput.vue";

export default {
    name: "batch-upload",
    components: {
        FloatingInput,
    },
    data() {
        return {
            user: this.$store.state.auth.user,
            previewImage: "/images/upload-icon.png",
            file_total_count: "",
            file_total_amount: 0,
            file_exchange_rate: "",
            formUpload: new Form({
                file: {},
                save_permission: 0,
                progress: 0,
                disabled: true,
            }),
            form: new Form({
                total_count: "",
                total_amount: "",
                exchange_rate: "",
            }),
            file_size: 0,
            file_size_total: 0,
            file_size_conversion: "KB",
        };
    },
    methods: {
        resetForm() {
            this.form.reset();
            this.formUpload.reset();
            this.formUpload.progress = 0;
            this.file_size_total = 0;
            this.file_size_progress = 0;
            this.file_size_conversion = "KB";
        },
        pickFile() {
            let input = this.$refs.transactionFile;
            let file = input.files;

            this.file_size_conversion = "KB";
            this.file_size_total = 0;
            this.file_size_progress = 0;
            if (file && file[0]) {
                this.formUpload.file = file[0];
                if (this.formUpload.file !== "") {
                    console.log("file selected", this.formUpload);
                    console.log("reader", file[0]);

                    this.file_size_total = this.formUpload.file.size / 1000;

                    if (
                        this.file_size_total >= 100000 ||
                        this.file_size_total >= 1000
                    ) {
                        this.file_size_total /= 1000;
                        this.file_size_conversion = "MB";
                    }
                    this.formUpload
                        .post("/api/read-file", {
                            onUploadProgress: (progressEvent) => {
                                const total = progressEvent.total;
                                const totalLength =
                                    progressEvent.lengthComputable
                                        ? total
                                        : null;
                                console.log(progressEvent);
                                if (totalLength !== null) {
                                    this.formUpload.progress = Math.round(
                                        (progressEvent.loaded * 100) / total
                                    );

                                    this.file_size_progress =
                                        this.file_size_conversion == "KB"
                                            ? Math.round(progressEvent.loaded) /
                                              1000
                                            : Math.round(progressEvent.loaded) /
                                              1000000;

                                    this.formUpload.disabled = false;
                                }
                            },
                        })
                        .then((data) => {
                            this.isLoading = false;
                            console.log('titi', data.data.data);
                            const result = data.data.data;
                            this.file_total_count = result.total_count;
                            this.file_total_amount = result.total_amount;
                            this.file_exchange_rate = result.exchange_rate;
                            // this.form.errors.total_amount = "asdsa";
                        })
                        .catch((err) => {
                            console.log(err.message);
                        });

                    console.log(this.form.errors);
                }
            }
        },
        saveTransaction() {
            // console.log(
            //     this.form.total_count == this.file_total_count,
            //     this.form.total_count > 0,
            //     this.form.total_amount == this.file_total_amount,
            //     this.form.total_amount != 0,
            //     this.form.exchange_rate == this.file_exchange_rate,

            //     this.form.total_amount
            // );
            if (this.formUpload.disabled == true) return;
            if (
                this.form.total_count == this.file_total_count &&
                this.form.total_count > 0 &&
                this.form.total_amount == this.file_total_amount &&
                this.form.total_amount != 0 &&
                this.form.exchange_rate == this.file_exchange_rate
            ) {
                console.log("save permission", this.formUpload.save_permission);
                this.$Progress.start();
                // if (this.formUpload.save_permission != false) {
                console.log("success");
                this.formUpload.save_permission = 1;
                this.pickFile();
                this.$moshaToast("Batch File has been successfully saved.", {
                    showIcon: true,
                    hideProgressBar: true,
                    timeout: 2000,
                    transition: "slide",
                    type: "success",
                    position: "top-right",
                });

                this.$Progress.finish();
                setTimeout(() => {
                    this.resetForm();
                    // this.backToUserPage();
                }, 1500);
                // this.pickFile();
            } else {
                this.$Progress.fail();

                this.resetForm();

                this.$moshaToast(
                    {
                        title: "Opps!",
                        description:
                            "Something went wrong in saving the batch file.",
                    },
                    {
                        type: "danger",
                        showIcon: true,
                        hideProgressBar: true,
                        position: "top-right",
                    }
                );
            }
            // }
        },
    },
    created() {},
};
</script>
