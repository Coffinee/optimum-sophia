<template>
    <div class="form-group">
        <label for="input1" class="col-sm-12">
            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{
                label
            }}</span>
        </label>
        <v-select
            id="customVSelect"
            class="mt-n3"
            v-model="selected"
            :options="options"
            :placeholder="placeholder"
            :loading="loading"
            :searchable="searchable"
            :value="selected"
        >
        </v-select>
    </div>
</template>

<script>
export default {
    name: "DropdownWithSearch",
    props: {
        label: {
            type: String,
            default: "",
        },
        loading: {
            type: Boolean,
            default: false,
        },
        options: {
            type: Array,
            default: [],
        },
        placeholder: {
            type: String,
            default: "",
        },
        searchable: {
            type: Boolean,
            default: true,
        },
        selected: {
            type: String,
            default: "",
        },
    },
};
</script>
