<template>
    <div>
        <form @submit.prevent class="animate__animated animate__fadeIn">
            <div class="row justify-content-between">
                <div class="col-md-6 mb-2">
                    <h4 class="form-banner">
                        {{ this.form.remitter_details.ref }}
                    </h4>
                </div>
                <div class="col-md-3 mb-2 text-center mr-13">
                    <button type="button" class="btn-red">
                        SEARCH REMITTER
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="px-4 py-1 col-md-12">
                    <div class="row g-1">
                        <div
                            v-for="(item, index) in remitter_fields"
                            :key="index"
                            class="col-md-3 mb-3"
                        >
                            <div
                                class="form-floating"
                                v-if="item.type.value != 'select'"
                            >
                                <input
                                    @input="
                                        item.value =
                                            $event.target.value.toUpperCase()
                                    "
                                    v-model="item.value"
                                    :type="
                                        item.type.value == 'number'
                                            ? 'text'
                                            : item.type.value
                                    "
                                    class="form-control"
                                    :maxlength="item.width"
                                    :id="item.label"
                                    @keypress="
                                        item.type == 'number' ? isNumber() : ''
                                    "
                                    :class="{
                                        'is-invalid': form.errors.has('label'),
                                    }"
                                />
                                <label :for="item.label"
                                    >{{ item.label }}
                                    <span v-show="item.require == true">*</span>
                                </label>
                            </div>

                            <span v-else>
                                <span v-if="item.label == 'COUNTRY'">
                                    <div class="form-floating vs">
                                        <v-select
                                            v-model="item.value"
                                            @option:selected="
                                                getStatesByCountry
                                            "
                                            :options="countries"
                                            placeholder="Select Country"
                                            id="country"
                                        />
                                        <label for="country"
                                            >Country
                                            <span v-show="item.require == true"
                                                >*</span
                                            ></label
                                        >
                                    </div>
                                </span>
                                <span v-else-if="item.label == 'STATE'">
                                    <div
                                        class="form-floating vs"
                                        v-if="isState"
                                    >
                                        <v-select
                                            v-model="item.value"
                                            @option:selected="getCityByState"
                                            :options="states"
                                            placeholder="Select State"
                                            id="states"
                                        />
                                        <label for="states"
                                            >State
                                            <span v-show="item.require == true"
                                                >*</span
                                            ></label
                                        >
                                    </div>
                                    <div
                                        class="form-floating vs"
                                        v-if="!isState"
                                    >
                                        <v-select
                                            v-model="item.value"
                                            @option:selected="getCityByState"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('region'),
                                            }"
                                            :options="regions"
                                            placeholder="Region"
                                            id="region"
                                        />
                                        <label for="region"
                                            >Region
                                            <span v-show="item.require == true"
                                                >*</span
                                            ></label
                                        >
                                    </div>
                                    <HasError :form="form" field="region" />
                                </span>
                                <span v-else-if="item.label == 'CITY'">
                                    <div class="form-floating vs">
                                        <v-select
                                            v-model="item.value"
                                            :options="cities"
                                            placeholder="Select City/Municipality"
                                            id="city"
                                        />
                                        <label for="city"
                                            >City/Municipality
                                            <span v-show="item.require == true"
                                                >*</span
                                            ></label
                                        >
                                    </div>
                                </span>
                                <span
                                    v-else-if="
                                        item.label == 'NATIONALITY/CITIZENSHIP'
                                    "
                                >
                                    <div class="form-floating vs">
                                        <v-select
                                            v-model="item.value"
                                            :options="nationality"
                                            placeholder="Select NATIONALITY/CITIZENSHIP"
                                            id="city"
                                        />
                                        <label for="city"
                                            >City/Municipality
                                            <span v-show="item.require == true"
                                                >*</span
                                            ></label
                                        >
                                    </div>
                                </span>

                                <span v-else>
                                    <div class="form-floating">
                                        <select
                                            v-model="item.value"
                                            name=""
                                            id=""
                                            class="custom-select form-control border-radius-0"
                                        >
                                            <option value="" selected disabled>
                                                Select {{ item.label }}
                                            </option>
                                            <option
                                                v-for="(
                                                    option, ind
                                                ) in item.tags"
                                                :key="ind"
                                                :value="option.text"
                                            >
                                                {{ option.text.toUpperCase() }}
                                            </option>
                                        </select>
                                        <label :for="item.label"
                                            >{{ item.label }}
                                            <span v-show="item.require == true"
                                                >*</span
                                            ></label
                                        >
                                    </div>
                                </span>
                            </span>

                            <HasError
                                :form="form"
                                :field="'biller_fields.' + index + '.value'"
                            />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-3 mb-3">
                            <div class="form-floating vs">
                                <v-select
                                    v-model="form.source_of_fund"
                                    @option:selected="getFundsFields"
                                    :options="funds"
                                    :class="{
                                        'is-invalid':
                                            form.errors.has('source_of_fund'),
                                    }"
                                    placeholder="Select Source of Funds"
                                    id="civil_status"
                                />
                                <label for="civil_status"
                                    >Source of Funds <span>*</span></label
                                >
                            </div>
                            <HasError :form="form" field="source_of_fund" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <FloatingInput
                                v-model="form.purpose"
                                label="Purpose of Remittance"
                                name="purpose"
                                :isInvalid="form.errors.has('purpose')"
                                :required="true"
                                placeholder=""
                            ></FloatingInput>
                            <HasError :form="form" field="purpose" />
                        </div>
                        <div class="col-md-3 mb-3">
                            <FloatingInput
                                v-model="form.relationship"
                                label="Relationship to the Beneficiary"
                                name="relationship"
                                :isInvalid="form.errors.has('relationship')"
                                :required="true"
                                placeholder=""
                            ></FloatingInput>
                            <HasError :form="form" field="relationship" />
                        </div>
                        <div class="col-md-2 mb-3">
                            <button
                                type="button"
                                @click="openModal"
                                class="btn-orange"
                            >
                                UPLOAD ID / DOCUMENTS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-between mt-3">
                <div class="col-md-6 mb-4">
                    <h4 class="form-banner">BILLER DETAILS</h4>
                </div>
            </div>
            <div class="row">
                <div class="px-4 py-1 col-md-12">
                    <div class="row g-2">
                        <div class="col-md-5 mb-3">
                            <div class="form-floating vs">
                                <v-select
                                    v-model="form.biller_name"
                                    :options="billers"
                                    :class="{
                                        'is-invalid':
                                            form.errors.has('biller_name'),
                                    }"
                                    @option:selected="getBillerCustomFields"
                                    placeholder="Select Biller"
                                    id="biller_name"
                                />
                                <label for="biller_name"
                                    >Biller Name <span>*</span></label
                                >
                            </div>
                            <HasError :form="form" field="biller_name" />
                        </div>
                    </div>

                    <div class="row g-1">
                        <div
                            v-for="(item, index) in form.biller_fields"
                            :key="index"
                            class="col-md-3 mb-3"
                        >
                            <div
                                class="form-floating"
                                v-if="item.type.value != 'select'"
                            >
                                <input
                                    @input="
                                        item.value =
                                            $event.target.value.toUpperCase()
                                    "
                                    v-model="item.value"
                                    :type="
                                        item.type.value == 'number'
                                            ? 'text'
                                            : item.type.value
                                    "
                                    class="form-control"
                                    :maxlength="item.width"
                                    :id="item.label"
                                    @keypress="
                                        item.type == 'number' ? isNumber() : ''
                                    "
                                    :class="{
                                        'is-invalid': form.errors.has('label'),
                                    }"
                                />
                                <label :for="item.label"
                                    >{{ item.label }}
                                </label>
                            </div>

                            <span v-else>
                                <span v-if="item.label == 'COUNTRY'">
                                    <div class="form-floating vs">
                                        <v-select
                                            v-model="item.value"
                                            @option:selected="
                                                getStatesByCountry
                                            "
                                            :options="countries"
                                            placeholder="Select Country"
                                            id="country"
                                        />
                                        <label for="country"
                                            >Country <span>*</span></label
                                        >
                                    </div>
                                </span>
                                <span v-else-if="item.label == 'STATE'">
                                    <div
                                        class="form-floating vs"
                                        v-if="isState"
                                    >
                                        <v-select
                                            v-model="item.value"
                                            @option:selected="getCityByState"
                                            :options="states"
                                            placeholder="Select State"
                                            id="states"
                                        />
                                        <label for="states"
                                            >State <span>*</span></label
                                        >
                                    </div>
                                    <div
                                        class="form-floating vs"
                                        v-if="!isState"
                                    >
                                        <v-select
                                            v-model="item.value"
                                            @option:selected="getCityByState"
                                            :class="{
                                                'is-invalid':
                                                    form.errors.has('region'),
                                            }"
                                            :options="regions"
                                            placeholder="Region"
                                            id="region"
                                        />
                                        <label for="region"
                                            >Region <span>*</span></label
                                        >
                                    </div>
                                    <HasError :form="form" field="region" />
                                </span>
                                <span v-else-if="item.label == 'CITY'">
                                    <div class="form-floating vs">
                                        <v-select
                                            v-model="item.value"
                                            :options="cities"
                                            placeholder="Select City/Municipality"
                                            id="city"
                                        />
                                        <label for="city"
                                            >City/Municipality
                                            <span>*</span></label
                                        >
                                    </div>
                                </span>

                                <span v-else>
                                    <div class="form-floating">
                                        <select
                                            v-model="item.value"
                                            name=""
                                            id=""
                                            class="custom-select form-control border-radius-0"
                                        >
                                            <option value="" selected disabled>
                                                Select {{ item.label }}
                                            </option>
                                            <option
                                                v-for="(
                                                    option, ind
                                                ) in item.tags"
                                                :key="ind"
                                                :value="option.text"
                                            >
                                                {{ option.text.toUpperCase() }}
                                            </option>
                                        </select>
                                        <label :for="item.label"
                                            >{{ item.label }}
                                            <span
                                                v-show="item.require == 'true'"
                                                >*</span
                                            ></label
                                        >
                                    </div>
                                </span>
                            </span>
                            <HasError
                                :form="form"
                                :field="'biller_fields.' + index + '.value'"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-between mt-3">
                <div class="col-md-6 mb-4">
                    <h4 class="form-banner">REMITTANCE DETAILS</h4>
                </div>
            </div>
            <div class="px-4 col-md-12">
                <div class="row g-2 remittance">
                    <div class="col-md-2 mb-3">
                        <div class="form-floating vs">
                            <v-select
                                v-model="form.original_currency"
                                :options="newCurrency"
                                :disabled="billerSelected"
                                @option:selected="getRate"
                                placeholder="Select Currency"
                                id="original_currency"
                            />
                            <label for="original_currency">Currency</label>
                            <HasError :form="form" field="original_currency" />
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <FloatingInput
                            v-model="form.original_amount"
                            @keypress="isNumber()"
                            @keyup.enter="
                                calculateRemittance(selectedNewCurrency)
                            "
                            maxlength="9"
                            type="text"
                            label="Amount"
                            name="original_amount"
                            :disabled="currencySelected"
                            :isInvalid="form.errors.has('original_amount')"
                            placeholder=""
                        ></FloatingInput>
                        <HasError :form="form" field="original_amount" />
                    </div>
                    <span class="operation">x</span>
                    <div class="col-md-2 mb-3">
                        <FloatingInput
                            v-model="form.exchange_rate"
                            type="text"
                            label="Exchange Rate"
                            :disabled="true"
                            name="exchange_rate"
                            :isInvalid="form.errors.has('exchange_rate')"
                            placeholder=""
                        ></FloatingInput>
                        <HasError :form="form" field="exchange_rate" />
                    </div>
                    <span class="operation">{{
                        form.type_of_charging == "add" ? "+" : "-"
                    }}</span>
                    <div class="col-md-2 mb-3">
                        <FloatingInput
                            v-model="form.service_fee"
                            type="number"
                            :disabled="true"
                            label="Service Fee"
                            name="service_fee"
                            :isInvalid="form.errors.has('service_fee')"
                            placeholder="0"
                        ></FloatingInput>
                        <HasError :form="form" field="service_fee" />
                    </div>
                    <span class="operation">=</span>
                    <div class="col-md-2 mb-3">
                        <FloatingInput
                            v-model="form.gross_amount"
                            type="text"
                            :disabled="true"
                            label="Total Amount"
                            name="gross_amount"
                            :isInvalid="form.errors.has('gross_amount')"
                            placeholder=""
                        ></FloatingInput>
                        <HasError :form="form" field="gross_amount" />
                    </div>
                </div>
            </div>
            <div class="px-4 col-md-12">
                <div class="row g-2 remittance">
                    <div class="col-2 col-sm-2 col-md-8"></div>
                    <div class="col-10 col-sm-10 col-md-2 mb-3 ml-5">
                        <FloatingInput
                            v-model="form.net_amount"
                            type="text"
                            :disabled="true"
                            label="Net Amount"
                            name="net_amount"
                            :isInvalid="form.errors.has('net_amount')"
                            placeholder=""
                        ></FloatingInput>
                        <HasError :form="form" field="net_amount" />
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mt-5 mb-5">
                <div class="col-6 col-md-2 col-sm-6">
                    <button
                        type="button"
                        @click="backToUserPage"
                        class="btn-cancel"
                    >
                        RESET
                    </button>
                </div>
                <div class="col-6 col-md-2 col-sm-6">
                    <button
                        :disabled="form.busy"
                        type="button"
                        class="btn-save"
                        @click.prevent="showSummary"
                    >
                        PROCEED
                    </button>
                </div>
            </div>
        </form>
        <RightSideModal
            icon="bi bi-credit-card"
            :widthModal="'35%'"
            :heightModal="'100vh'"
        >
            <template v-slot:header>SOURCE OF FUNDS</template>
            <template v-slot:body>
                <div class="row py-3 justify-content-center">
                    <div class="col-md-11 mb-3">
                        <div class="form-floating vs">
                            <v-select
                                v-model="form.source_of_fund"
                                :options="funds"
                                @option:selected="getFundsFields"
                                placeholder="Select Source of Funds"
                                id="civil_status"
                            />
                            <label for="civil_status"
                                >Source of Funds <span>*</span></label
                            >
                        </div>
                    </div>
                    <div
                        v-for="(item, index) in form.source_of_fund_fields"
                        :key="index"
                        class="col-md-11 mb-3"
                    >
                        <div
                            class="form-floating"
                            v-if="
                                item.type == 'text' ||
                                item.type == 'number' ||
                                item.type == 'date' ||
                                item.type == 'email' ||
                                item.type == 'select'
                            "
                        >
                            <input
                                v-model="item.value"
                                @input="
                                    item.value =
                                        $event.target.value.toUpperCase()
                                "
                                v-if="
                                    item.type == 'text' ||
                                    item.type == 'number' ||
                                    item.type == 'date' ||
                                    item.type == 'email'
                                "
                                :type="item.type"
                                class="form-control"
                                :id="item.label"
                                :class="{
                                    'is-invalid': form.errors.has(
                                        'source_of_fund_fields.' +
                                            index +
                                            '.value'
                                    ),
                                }"
                            />
                            <select
                                v-if="item.type == 'select'"
                                v-model="item.value"
                                name=""
                                id=""
                                class="custom-select form-control border-radius-0"
                                :class="{
                                    'is-invalid': form.errors.has(
                                        'source_of_fund_fields.' +
                                            index +
                                            '.value'
                                    ),
                                }"
                            >
                                <option value="" selected disabled>
                                    Select {{ item.label }}
                                </option>
                                <option
                                    v-for="(option, ind) in item.options"
                                    :key="ind"
                                    :value="option.text"
                                >
                                    {{ option.text }}
                                </option>
                            </select>
                            <label :for="item.label"
                                >{{ item.label }}
                                <span v-show="item.require == true"
                                    >*</span
                                ></label
                            >
                        </div>
                        <DropFile
                            :class="{
                                'dashed-invalid': form.errors.has(
                                    'source_of_fund_fields.' + index + '.value'
                                ),
                            }"
                            :batch="form.batch_number"
                            :fileType="item.type"
                            :name="
                                item.label.split(' ').join('_').toLowerCase()
                            "
                            v-if="item.type == 'file' || item.type == 'image'"
                            :label="item.label"
                        />
                        <HasError
                            :form="form"
                            :field="'source_of_fund_fields.' + index + '.value'"
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
                            CANCEL
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="button"
                            @click="closeModal"
                            class="btn-save"
                        >
                            OK
                        </button>
                    </div>
                </div>
            </template>
        </RightSideModal>
        <MiddleModal icon="" :widthModal="'70%'" :heightModal="'850px'">
            <template v-slot:header>TRANSACTION SUMMARY</template>
            <template v-slot:body>
                <div class="px-5 position-relative">
                    <div class="row">
                        <div class="col-md-4">
                            <GroupHalfInput
                                v-model="preview_transaction_name"
                                label="TRANSACTION TYPE"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                            ></GroupHalfInput>
                        </div>
                    </div>
                    <div class="row mt-3 position-absolute start-0">
                        <div class="col-md-6 mb-1">
                            <h4 class="form-banner">REMITTER DETAILS</h4>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_fullname"
                                label="REMITTER NAME"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                            ></GroupHalfInput>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_mobile_number"
                                label="MOBILE NUMBER"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                            ></GroupHalfInput>
                        </div>
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_email"
                                label="EMAIL ADDRESS"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                            ></GroupHalfInput>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <GroupHalfInput
                                v-model="preview_address"
                                label="ADDRESS"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                            ></GroupHalfInput>
                        </div>
                    </div>

                    <!-- <div class="row mt-5">
                        <div
                            class="col-md-6"
                            v-for="(item, index) in preview_remitter_details"
                            :key="index"
                        >
                            <GroupHalfInput
                                v-show="
                                    preview_remitter_details_array.includes(
                                        item.label
                                    )
                                "
                                :label="item.label"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                                :modelValue="item.value"
                            ></GroupHalfInput>
                        </div>
                    </div> -->
                    <div class="row mt-3 position-absolute start-0">
                        <div class="col-md-6 mb-1">
                            <h4 class="form-banner">BILLER DETAILS</h4>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_biller_name"
                                label="BILLER NAME"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                            ></GroupHalfInput>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div
                            class="col-md-6"
                            v-for="(
                                item, index
                            ) in preview_biller_custom_fields"
                            :key="index"
                        >
                            <GroupHalfInput
                                :label="item.label"
                                type="text"
                                :isDisabled="true"
                                name="company-name"
                                :modelValue="item.value"
                            ></GroupHalfInput>
                        </div>
                    </div>

                    <div class="row mt-3 position-absolute start-0">
                        <div class="col-md-6 mb-1">
                            <h4 class="form-banner">REMITTANCE DETAILS</h4>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_currency"
                                label="CURRENCY"
                                type="text"
                                :isDisabled="true"
                                name="curreny"
                            ></GroupHalfInput>
                        </div>
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_equivalent_amount"
                                label="EQUIVALENT AMOUNT"
                                type="text"
                                :isDisabled="true"
                                name="curreny"
                            ></GroupHalfInput>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_exchange_rate"
                                label="EXCHANGE RATE"
                                type="text"
                                :isDisabled="true"
                                name="curreny"
                            ></GroupHalfInput>
                        </div>
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_service_fee"
                                :label="preview_operation + ': SERVICE FEE'"
                                type="text"
                                :isDisabled="true"
                                name="curreny"
                            ></GroupHalfInput>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_total"
                                label="TOTAL AMOUNT"
                                type="text"
                                :isDisabled="true"
                                name="curreny"
                            ></GroupHalfInput>
                        </div>
                        <div class="col-md-6">
                            <GroupHalfInput
                                v-model="preview_net"
                                label="NET AMOUNT"
                                type="text"
                                :isDisabled="true"
                                name="curreny"
                            ></GroupHalfInput>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <div class="col-md-3">
                            <button
                                data-dismiss="modal"
                                type="button"
                                @click.prevent="closeSummaryModal"
                                class="btn-cancel"
                            >
                                CANCEL
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button
                                :disabled="form.busy"
                                type="button"
                                @click.prevent="saveTransaction"
                                class="btn-save"
                            >
                                CONFIRM
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </MiddleModal>
    </div>
</template>

<script>
import Form from "vform";
import { HasError } from "vform/src/components/bootstrap5";
import FloatingInput from "@/components/Misc/Inputs/FloatingInput.vue";
import DropFile from "@/components/Misc/Inputs/DropFile.vue";
import RightSideModal from "@/components/Misc/Modals/RightSideModal.vue";
import MiddleModal from "@/components/Misc/Modals/MiddleModal.vue";
import GroupHalfInput from "@/components/Misc/Inputs/GroupHalfInput.vue";
import { errorMessage, successMessage, warningMessage } from "@/utilities.js";
export default {
    name: "BPForm",
    props: {
        funds: {
            type: Array,
            default: {},
        },
        currencies: {
            type: Array,
            default: {},
        },
        billers: {
            type: Array,
            default: {},
        },
        countries: {
            type: Array,
            default: {},
        },
        refNumber: {
            type: String,
            default: "",
        },
        batch_number: {
            type: String,
            default: Math.random().toString(36).slice(2, 10).toUpperCase(),
        },
        addOrLess: {
            type: String,
            default: "",
        },
        transactionTypeId: {
            type: Number,
            default: 1,
        },
    },
    components: {
        FloatingInput,
        HasError,
        RightSideModal,
        DropFile,
        MiddleModal,
        GroupHalfInput,
    },
    data() {
        return {
            user: this.$store.state.auth.user,
            editmode: false,
            currencySelected: true,
            billerSelected: true,
            allTrasactionType: [],
            cities: [],
            states: [],
            regions: [],
            hasFile: false,
            isState: true,
            filesUploaded: [],
            genders: [
                { label: "Male", value: "male" },
                { label: "Female", value: "female" },
            ],
            civilStatus: [
                { label: "Single", value: "single" },
                { label: "Married", value: "married" },
                { label: "Divorced", value: "divorced" },
                { label: "Separated", value: "separated" },
                { label: "Widowed", value: "widowed" },
            ],
            custom_fields: [],
            nationality: [
                { label: "Afghan", value: "Afghan" },
                { label: "Albanian", value: "Albanian" },
                { label: "Amderian", value: "Amderian" },
                { label: "American", value: "American" },
                { label: "Andorran", value: "Andorran" },
                { label: "Angolan", value: "Angolan" },
                { label: "Antiguans", value: "Antiguans" },
                { label: "Argentinean", value: "Argentinean" },
                { label: "Armenian", value: "Armenian" },
                { label: "Australian", value: "Australian" },
                { label: "Austrian", value: "Austrian" },
                { label: "Azerbaijani", value: "Azerbaijani" },
                { label: "Bahamian", value: "Bahamian" },
                { label: "Bahraini", value: "Bahraini" },
                { label: "Bangladeshi", value: "Bangladeshi" },
                { label: "Barbadian", value: "Barbadian" },
                { label: "Barbudans", value: "Barbudans" },
                { label: "Batswana", value: "Batswana" },
                { label: "Belarusian", value: "Belarusian" },
                { label: "Bemdian", value: "Bemdian" },
                { label: "Belizean", value: "Belizean" },
                { label: "Beninese", value: "Beninese" },
                { label: "Bhutanese", value: "Bhutanese" },
                { label: "Bolivian", value: "Bolivian" },
                { label: "Bosnian", value: "Bosnian" },
                { label: "Brazilian", value: "Brazilian" },
                { label: "British", value: "British" },
                { label: "Bruneian", value: "Bruneian" },
                { label: "Bumdarian", value: "Bumdarian" },
                { label: "Burkinabe", value: "Burkinabe" },
                { label: "Burmese", value: "Burmese" },
                { label: "Burundian", value: "Burundian" },
                { label: "Cambodian", value: "Cambodian" },
                { label: "Cameroonian", value: "Cameroonian" },
                { label: "Cape Verdean", value: "Cape Verdean" },
                { label: "Central African", value: "Central African" },
                { label: "Chadian", value: "Chadian" },
                { label: "Chilean", value: "Chilean" },
                { label: "Chinese", value: "Chinese" },
                { label: "Colombian", value: "Colombian" },
                { label: "Comoran", value: "Comoran" },
                { label: "Congolese", value: "Congolese" },
                { label: "Costa Rican", value: "Costa Rican" },
                { label: "Croatian", value: "Croatian" },
                { label: "Cuban", value: "Cuban" },
                { label: "Cypriot", value: "Cypriot" },
                { label: "Czech", value: "Czech" },
                { label: "Danish", value: "Danish" },
                { label: "Djibouti", value: "Djibouti" },
                { label: "Dominican", value: "Dominican" },
                { label: "Dutch", value: "Dutch" },
                { label: "East Timorese", value: "East Timorese" },
                { label: "Ecuadorean", value: "Ecuadorean" },
                { label: "Egyptian", value: "Egyptian" },
                { label: "Equatorial Guinean", value: "Equatorial Guinean" },
                { label: "Eritrean", value: "Eritrean" },
                { label: "Estonian", value: "Estonian" },
                { label: "Ethiopian", value: "Ethiopian" },
                { label: "Filipino", value: "Filipino" },
                { label: "Finnish", value: "Finnish" },
                { label: "French", value: "French" },
                { label: "Gabonese", value: "Gabonese" },
                { label: "Gambian", value: "Gambian" },
                { label: "Georgian", value: "Georgian" },
                { label: "German", value: "German" },
                { label: "Ghanaian", value: "Ghanaian" },
                { label: "Greek", value: "Greek" },
                { label: "Grenadian", value: "Grenadian" },
                { label: "Guatemalan", value: "Guatemalan" },
                { label: "Guinea-Bissauan", value: "Guinea-Bissauan" },
                { label: "Guinean", value: "Guinean" },
                { label: "Guyanese", value: "Guyanese" },
                { label: "Haitian", value: "Haitian" },
                { label: "Herzegovinian", value: "Herzegovinian" },
                { label: "Honduran", value: "Honduran" },
                { label: "Hungarian", value: "Hungarian" },
                { label: "Icelander", value: "Icelander" },
                { label: "Indian", value: "Indian" },
                { label: "Indonesian", value: "Indonesian" },
                { label: "Iranian", value: "Iranian" },
                { label: "Iraqi", value: "Iraqi" },
                { label: "Irish", value: "Irish" },
                { label: "Israeli", value: "Israeli" },
                { label: "Italian", value: "Italian" },
                { label: "Ivorian", value: "Ivorian" },
                { label: "Jamaican", value: "Jamaican" },
                { label: "Japanese", value: "Japanese" },
                { label: "Jordanian", value: "Jordanian" },
                { label: "Kazakhstani", value: "Kazakhstani" },
                {
                    label: "Kittian and Nevisian",
                    value: "Kittian and Nevisian",
                },
                { label: "Kuwaiti", value: "Kuwaiti" },
                { label: "Kyrgyz", value: "Kyrgyz" },
                { label: "Laotian", value: "Laotian" },
                { label: "Latvian", value: "Latvian" },
                { label: "Lebanese", value: "Lebanese" },
                { label: "Liberian", value: "Liberian" },
                { label: "Libyan", value: "Libyan" },
                { label: "Liechtensteiner", value: "Liechtensteiner" },
                { label: "Lithuanian", value: "Lithuanian" },
                { label: "Luxembourger", value: "Luxembourger" },
                { label: "Macedonian", value: "Macedonian" },
                { label: "Malagasy", value: "Malagasy" },
                { label: "Malawian", value: "Malawian" },
                { label: "Malaysian", value: "Malaysian" },
                { label: "Maldivan", value: "Maldivan" },
                { label: "Malian", value: "Malian" },
                { label: "Maltese", value: "Maltese" },
                { label: "Marshallese", value: "Marshallese" },
                { label: "Mauritanian", value: "Mauritanian" },
                { label: "Mauritian", value: "Mauritian" },
                { label: "Mexican", value: "Mexican" },
                { label: "Micronesian", value: "Micronesian" },
                { label: "Moldovan", value: "Moldovan" },
                { label: "Monacan", value: "Monacan" },
                { label: "Mongolian", value: "Mongolian" },
                { label: "Moroccan", value: "Moroccan" },
                { label: "Mosotho", value: "Mosotho" },
                { label: "Motswana", value: "Motswana" },
                { label: "Mozambican", value: "Mozambican" },
                { label: "Namibian", value: "Namibian" },
                { label: "Nauruan", value: "Nauruan" },
                { label: "Nepalese", value: "Nepalese" },
                { label: "New Zealander", value: "New Zealander" },
                { label: "Ni-Vanuatu", value: "Ni-Vanuatu" },
                { label: "Nicaraguan", value: "Nicaraguan" },
                { label: "Nigerien", value: "Nigerien" },
                { label: "North Korean", value: "North Korean" },
                { label: "Northern Irish", value: "Northern Irish" },
                { label: "Norwegian", value: "Norwegian" },
                { label: "Omani", value: "Omani" },
                { label: "Pakistani", value: "Pakistani" },
                { label: "Palauan", value: "Palauan" },
                { label: "Panamanian", value: "Panamanian" },
                { label: "Papua New Guinean", value: "Papua New Guinean" },
                { label: "Paraguayan", value: "Paraguayan" },
                { label: "Polish", value: "Polish" },
                { label: "Portuguese", value: "Portuguese" },
                { label: "Qatari", value: "Qatari" },
                { label: "Romanian", value: "Romanian" },
                { label: "Russian", value: "Russian" },
                { label: "Rwandan", value: "Rwandan" },
                { label: "Saint Lucian", value: "Saint Lucian" },
                { label: "Salvadoran", value: "Salvadoran" },
                { label: "Samoan", value: "Samoan" },
                { label: "San Marinese", value: "San Marinese" },
                { label: "Sao Tomean", value: "Sao Tomean" },
                { label: "Saudi", value: "Saudi" },
                { label: "Scottish", value: "Scottish" },
                { label: "Senegalese", value: "Senegalese" },
                { label: "Serbian", value: "Serbian" },
                { label: "Seychellois", value: "Seychellois" },
                { label: "Sierra Leonean", value: "Sierra Leonean" },
                { label: "Singaporean", value: "Singaporean" },
                { label: "Slovakian", value: "Slovakian" },
                { label: "Slovenian", value: "Slovenian" },
                { label: "Solomon Islander", value: "Solomon Islander" },
                { label: "Somali", value: "Somali" },
                { label: "South African", value: "South African" },
                { label: "South Korean", value: "South Korean" },
                { label: "Sri Lankan", value: "Sri Lankan" },
                { label: "Sudanese", value: "Sudanese" },
                { label: "Surinamer", value: "Surinamer" },
                { label: "Swazi", value: "Swazi" },
                { label: "Swedish", value: "Swedish" },
                { label: "Swiss", value: "Swiss" },
                { label: "Syrian", value: "Syrian" },
                { label: "Taiwanese", value: "Taiwanese" },
                { label: "Tajik", value: "Tajik" },
                { label: "Tanzanian", value: "Tanzanian" },
                { label: "Thai", value: "Thai" },
                { label: "Togolese", value: "Togolese" },
                { label: "Tongan", value: "Tongan" },
                {
                    label: "Trinidadian or Tobagonian",
                    value: "Trinidadian or Tobagonian",
                },
                { label: "Tunisian", value: "Tunisian" },
                { label: "Turkish", value: "Turkish" },
                { label: "Tuvaluan", value: "Tuvaluan" },
                { label: "Ugandan", value: "Ugandan" },
                { label: "Ukrainian", value: "Ukrainian" },
                { label: "Uruguayan", value: "Uruguayan" },
                { label: "Uzbekistani", value: "Uzbekistani" },
                { label: "Venezuelan", value: "Venezuelan" },
                { label: "Vietnamese", value: "Vietnamese" },
                { label: "Welsh", value: "Welsh" },
                { label: "Yemenite", value: "Yemenite" },
                { label: "Zambian", value: "Zambian" },
                { label: "Zimbabwean", value: "Zimbabwean" },
            ],
            preview_remitter_details: [],
            preview_transaction_name: "",
            preview_fullname: "",
            preview_mobile_number: "",
            preview_email: "",
            preview_address: "",
            preview_biller_name: "",
            preview_biller_custom_fields: [],
            preview_currency: "",
            preview_equivalent_amount: "",
            preview_exchange_rate: "",
            preview_service_fee: "",
            preview_total: "",
            preview_net: "",
            preview_operation: "",

            form: new Form({
                reference_number: "",
                company_id: "",
                branch_id: "",
                branch_reference_number: this.$props.refNumber,
                user_id: "",
                transaction_type: "BP",
                remitter_id: "",
                batch_number: this.$props.batch_number,
                remitter_details: [],
                source_of_fund: "",
                purpose: "",
                relationship: "",
                biller_name: "",
                biller_fields: [],
                original_currency: "",
                original_amount: "",
                exchange_rate: "",
                gross_amount: "",
                operation: "add",
                service_fee: 0,
                net_amount: "",
                type_of_charging: this.$props.addOrLess,
                type_of_fee: "",
                source_of_fund_fields: [(value) => {}, (file) => []],
            }),
            oldCurrency: [],
            newCurrency: [],
            selectedNewCurrency: [],
            currencyAmountRanges: [],
            selectAPIoptions: [
                { label: "COUNTRY", value: this.countries },
                { label: "STATE", value: this.states },
                { label: "CITY", value: this.cities },
            ],
            optionHasApi: false,
            inbountToken: "",
            remitter_fields: [],
            preview_remitter_details_array: [
                "MOBILE NUMBER",
                "EMAIL ADDRESS",
                "ADDRESS",
            ],
        };
    },
    methods: {
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
        openModal() {
            jQuery("#modal-right-side").modal("show");
        },
        closeModal() {
            jQuery("#modal-right-side").modal("hide");
        },
        closeSummaryModal() {
            jQuery("#modal-middle").modal("hide");
        },
        showSummary() {
            this.form.remitter_details.fields = this.remitter_fields;

            this.form.biller_fields.map(function (field) {
                if (
                    field.label == "COUNTRY" ||
                    field.label == "STATE" ||
                    field.label == "CITY"
                ) {
                    field.value = field.value.label;
                }
            });
            this.$Progress.start();
            this.form
                .post("/api/validate-bp-transaction-fields")
                .then((data) => {
                    this.preview_remitter_details =
                        this.form.remitter_details.fields;
                    this.preview_transaction_name = "BILLS PAYMENT";

                    this.preview_fullname =
                        this.getRowByFieldslabel(
                            this.remitter_fields,
                            "LAST NAME"
                        ).value ?? "";
                    +", " +
                        this.getRowByFieldslabel(
                            this.remitter_fields,
                            "FIRST NAME"
                        ).value ?? "";

                    this.preview_mobile_number =
                        this.getRowByFieldslabel(
                            this.remitter_fields,
                            "MOBILE NUMBER"
                        ).value ?? "";

                    this.preview_gender =
                        this.getRowByFieldslabel(this.remitter_fields, "GENDER")
                            .value ?? "";

                    this.preview_email =
                        this.getRowByFieldslabel(
                            this.remitter_fields,
                            "EMAIL ADDRESS"
                        ).value ?? "";

                    this.preview_address =
                        this.getRowByFieldslabel(
                            this.remitter_fields,
                            "PRESENT/PERMANENT ADDRESS"
                        ).value ?? "";

                    this.preview_biller_name = this.form.biller_name.label;
                    this.preview_biller_custom_fields = this.form.biller_fields;
                    this.preview_currency = this.form.original_currency.label;
                    this.preview_equivalent_amount = this.form.original_amount;
                    this.preview_exchange_rate = this.form.exchange_rate;
                    this.preview_service_fee = this.form.service_fee;
                    this.preview_total = this.form.gross_amount;
                    this.preview_net = this.form.net_amount;
                    this.preview_operation = this.form.operation.toUpperCase();
                    jQuery("#modal-middle").modal("show");
                    this.$Progress.finish();
                })
                .catch((e) => {
                    errorMessage("Opps!", e.message, "top-right");
                    console.log(e.message);
                    this.$Progress.fail();
                });
        },
        getFundsFields(data) {
            this.form.source_of_fund_fields = JSON.parse(data.fields);
        },
        getStatesByCountry(data) {
            axios
                .get("/api/address/countries/" + data.value + "/states")
                .then((data) => {
                    this.isState = data.data.data.hasState;
                    if (this.isState) {
                        this.states = data.data.data.data;
                    } else {
                        this.regions = data.data.data.data;
                        this.provinces = data.data.data.provinces;
                    }
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        getCityByState(data) {
            axios
                .get("/api/address/states/" + data.value + "/cities")
                .then((data) => {
                    this.cities = data.data.data;
                })
                .catch((err) => {
                    console.log(err);
                });
        },
        getRowByFieldslabel(customer_details, field_name) {
            return customer_details.find(
                (element) => element.label === field_name
            );
        },
        saveAsN() {
            for (let i = 0; i < this.form.remitter_details.fields.length; i++) {
                for (let prop in this.form.remitter_details.fields[i]) {
                    if (
                        prop === "value" &&
                        typeof this.form.remitter_details.fields[i][prop] ===
                            "string"
                    ) {
                        this.form.remitter_details.fields[i][prop] =
                            this.form.remitter_details.fields[i][prop].replace(
                                /[Ññ]/g,
                                "n"
                            );
                    }
                }
            }
        },
        saveTransaction() {
            this.saveAsN();
            //
            this.$Progress.start();
            this.form
                .post("/api/transactions-bp")
                .then((data) => {
                    jQuery("#modal-middle").modal("hide");
                    successMessage(
                        "Added!",
                        "Transaction complited",
                        "top-right"
                    );
                    setTimeout(() => {
                        this.$router.push({
                            path: "/app/receipt",
                            query: {
                                transactionID: data.data.data.transaction_id,
                            },
                        });
                    }, 1500);

                    this.$Progress.finish();
                })
                .catch((e) => {
                    this.form.source_of_fund_fields.forEach((el, index) => {
                        if (
                            e.response.data.errors.hasOwnProperty(
                                "source_of_fund_fields." + index + ".value"
                            )
                        ) {
                            jQuery(".right_modal").modal("show");
                        }
                    });
                    this.$Progress.fail();
                    errorMessage("Opps!", e.message, "top-right");
                });
        },
        updateFundsData() {},
        getBillerCustomFields(data) {
            // console.log("billers", data.currency);

            this.oldCurrency = data.currency;

            console.log("currency", data);
            this.currencyAmountRanges = this.oldCurrency.map(function (item) {
                if (typeof item.amount_range === "string") {
                    item.amount_range = item.amount_range.split("-");

                    item.amount_range = {
                        min: parseInt(
                            item.amount_range[0].replace(/[\\"]/g, "")
                        ),
                        max: parseInt(item.amount_range[1]),
                    };
                }

                // return console.log("amount_range", item);
            });

            this.newCurrency = Object.values(
                data.currency.reduce((obj, item) => {
                    obj[item.label] = item;
                    return obj;
                }, {})
            );

            //BP FORM -> DROPDOWN CURRENCY -> CALCULATE AMOUNT TO GET SERVICE FEE

            this.form.biller_fields = JSON.parse(data.fields);

            //data.service_fee.currency
            this.billerSelected = false;
        },
        getRate(data) {
            if (data.rate == 0)
                warningMessage(
                    "Opps!",
                    "Seems like the currency selected has ZERO amount. Please double check the rates on Settings > Rate Management.",
                    "bottom-center"
                );

            this.selectedNewCurrency = data;
            this.original_currency = data.label.replace(/"/g, "");

            this.$props.currencies.map((currency) => {
                // console.log(data, currency);
                currency.label.includes(this.original_currency) == true
                    ? (this.form.exchange_rate = currency.rate)
                    : "";
            });

            this.currencySelected = false;
            this.calculateRemittance(data);
        },
        calculateRemittance(data) {
            var amount = parseFloat(this.form.original_amount);

            let tempServiceFee = 0;
            let tempTypeOfFee = "";
            let selectedCurrencyDetails = [];
            this.oldCurrency.map(function (item) {
                if (item.label == data.label) {
                    //not getting the right currency and range.
                    //must get all amount range, type_of_fee depending on the selected currency
                    selectedCurrencyDetails.push({
                        currency: item.label,
                        amount_range: item.amount_range,
                        type_of_fee: item.type_of_fee,
                        service_fee: item.service_fee,
                    });
                    if (item.amount_range == null) {
                        tempServiceFee = item.service_fee;
                    } else {
                        selectedCurrencyDetails.map(function (item) {
                            if (
                                amount >= item.amount_range.min &&
                                amount < item.amount_range.max
                            ) {
                                return (
                                    (tempServiceFee = item.service_fee),
                                    (tempTypeOfFee = item.type_of_fee)
                                );
                            }
                        });
                    }
                }
            });

            // console.log("type_of_fee", data.type_of_fee, data);
            // console.log(this.oldCurrency, data, tempServiceFee);

            if (amount == null)
                warningMessage(
                    "Opps!",
                    "Seems like the currency selected has ZERO amount. Please double check the rates on Settings > Rate Management.",
                    "bottom-center"
                );

            if (tempTypeOfFee == "PERCENTAGE") {
                this.form.service_fee = amount * (tempServiceFee / 100);
            } else {
                this.form.service_fee = tempServiceFee;
            }

            if (amount) {
                if (this.form.type_of_charging == "add") {
                    //get exchage_rate from rates
                    // this.form.exchange_rate = 1;
                    this.form.gross_amount =
                        amount * this.form.exchange_rate +
                        this.form.service_fee;

                    this.form.net_amount = this.$filters.formatAmount(
                        this.form.gross_amount - this.form.service_fee,
                        2,
                        true
                    );

                    this.form.gross_amount = this.$filters.formatAmount(
                        this.form.gross_amount,
                        2,
                        true
                    );
                } else {
                    this.form.gross_amount =
                        amount -
                        this.form.service_fee * this.form.exchange_rate;

                    this.form.net_amount = this.$filters.formatAmount(
                        this.form.gross_amount,
                        2,
                        true
                    );

                    this.form.gross_amount = this.$filters.formatAmount(
                        this.form.gross_amount,
                        2,
                        true
                    );
                }
            }
        },
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
                firstName.value + " " + middleName.value + ". " + lastName.value
            );
        },
    },
    async created() {
        axios
            .get("/api/get-inbound-api-token")
            .then((data) => {
                this.inbountToken = data.data.token;
            })
            .finally(async (e) => {
                await axios
                    .get(this.$eosUrl + "/api/outbound/get-custom-fields/", {
                        params: {
                            branch_id: this.user.branch_id,
                            transactionId: this.$props.transactionTypeId,
                        },
                        headers: {
                            Authorization: "Bearer " + this.inbountToken,
                        },
                    })
                    .then((data) => {
                        this.form.remitter_details = data.data.data[0];
                        this.remitter_fields = JSON.parse(
                            this.form.remitter_details.fields
                        );
                        console.log("test fields", this.form.remitter_details);
                    })
                    .catch((e) => {
                        errorMessage("Opps!", e.message, "top-right");
                    });
            });
    },
};
</script>
