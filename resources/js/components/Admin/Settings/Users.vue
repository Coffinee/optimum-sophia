<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 text-right text-white">USERS</h6>
                </div>
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-7">
                                    <!-- <Search v-model="searchInput"  icon="bi-search" name="search" placeholder="Search"></Search> -->
                                    <div class="form-group has-search">
                                        <i
                                            class="bi bi-search form-control-feedback"
                                        ></i>
                                        <input
                                            v-model="search"
                                            type="text"
                                            v-debounce:500ms="searchUser"
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
                                label="Add User"
                                @click="gotoForm"
                            ></ButtonOrange>
                        </div>
                    </div>
                    <div class="col-md-12  py-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" /></th>
                                    <th></th>
                                    <th>USERNAME</th>
                                    <th>NAME</th>
                                    <th>COMPANY NAME</th>
                                    <th>DATE ADDED</th>
                                    <th class="text-center th-action">
                                        ACTIONS
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader
                                    :showLoader="isLoading"
                                    :colLength="6"
                                ></TableLoader>
                                <tr v-for="item in users.data" :key="item.id">
                                    <td><input type="checkbox" /></td>
                                    <td class="text-center py-1">
                                        <img
                                            class="img-profile"
                                            :src="[
                                                item.profile_picture
                                                    ? '/uploads/50x50/' +
                                                      item.profile_picture
                                                    : '/uploads/default.png',
                                            ]"
                                            alt=""
                                        />
                                    </td>
                                    <td>{{ item.user_name }}</td>
                                    <td>
                                        {{ item.first_name }}
                                        {{ item.last_name }}
                                    </td>
                                    <td>{{ item.company_id }}</td>
                                    <td>
                                        {{
                                            $filters.dateFormat(item.created_at)
                                        }}
                                    </td>
                                    <td
                                        class="text-center d-flex justify-content-evenly align-content-center"
                                    >
                                        <router-link
                                            :to="{
                                                name: 'edit-user',
                                                params: {
                                                    data: JSON.stringify(item),
                                                },
                                            }"
                                            class="rounded-s-button"
                                            href="#"
                                        >
                                            <i class="bi bi-pencil-fill"></i>
                                        </router-link>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input table-button"
                                                type="checkbox"
                                                id="flexSwitchCheckChecked"
                                                @change.prevent="
                                                    setUserStatus(
                                                        item.id,
                                                        item.status
                                                    )
                                                "
                                                :checked="item.status"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { errorMessage, successMessage } from '@/utilities.js'
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
export default {
    name: "users",
    components: {
        ButtonOrange,
        ButtonBlack,
        TableLoader,
    },
    data() {
        return {
            user: this.$store.state.auth.user,
            search: "",
            isLoading: true,
            users: {},
        };
    },
    methods: {
        gotoForm() {
            this.$router.push("/app/settings/user/create");
        },
        searchUser() {},
        openModal() {},
        editUser(item) {
            const params = {
                user: item,
                editmode: true,
            };
            this.$router.push({
                path: this.$route.params.mainNavigation,
                query: params,
            });
        },
        setUserStatus() {},
        loadData() {
            axios
                .get("/api/users")
                .then((data) => {
                    this.isLoading = false;
                    this.users = data.data.data;
                })
                .catch(() => {
                    errorMessage("Opps!", "Something went wrong in fetching users.", "top-right")
                });
        },
    },
    created() {
        this.loadData();
    },
};
</script>
