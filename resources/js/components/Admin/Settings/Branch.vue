<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main  mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 text-right text-white">BRANCH</h6>
                </div>
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="form-group has-search">
                                        <i class="bi bi-search form-control-feedback"></i>
                                        <input type="text" name="search" class="form-control" placeholder="Search"/>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <ButtonBlack icon="bi bi-download" label="Export"></ButtonBlack>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <ButtonOrange icon="bi bi-plus-circle" label="Add Branch"  @click="gotoCreateBranch"></ButtonOrange>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">COMPANY NAME</th>
                                    <th scope="col">BRANCH NAME</th>
                                    <th scope="col">COUNTRY</th>
                                    <th scope="col">STATE/PROVINCE</th>
                                    <th scope="col">CITY</th>
                                    <th scope="col">REFERENCE NO.</th>
                                    <th scope="col">CURRENCY</th>
                                    <th scope="col">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader :showLoader="isLoading" :colLength="7"></TableLoader>
                                <tr v-if="showNoFound">
                                    <td colspan="7"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                                </tr>
                                <tr  v-for="branch in branches.data" :key="branch.id">
                                    <td> {{ branch.company.name }}</td>
                                    <td>{{ branch.name }}</td>
                                    <td>{{ branch.country_name.name }}</td>
                                    <td>{{ branch.state_name != null ? branch.state_name.name : (branch.province_name != null ? branch.province_name.name : '')  }}</td>
                                    <td>{{ branch.city_name != null ? branch.city_name.name : '' }}</td>
                                    <td>{{ branch.prefix }}</td>
                                    <td>
                                        <span v-for="item in branch.rates" :key="item.id">
                                            {{item.from_currency.currency +' - '+ item.to_currency.currency}},
                                        </span>
                                    </td>
                                    <td class="text-center ">
                                        <router-link :to="{ name: 'edit-branch', params: { data: JSON.stringify(branch)}}" class="rounded-s-button">
                                            <i class="bi bi-pencil-fill"></i>
                                        </router-link>
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
import Form from "vform";
import PageHeading from "@/components/Layouts/parts/PageHeading.vue";
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";

export default {
    name: "branch",
    components: {
        PageHeading,
        ButtonOrange,
        ButtonBlack,
        TableLoader
    },
    data() {
        return {
            user: this.$store.state.auth.user,
            branches: [],
            editmode: false,
            showNoFound: false,
            search: "",
            isLoading: true,
            form: new Form({
                company_id: "",
                id: "",
                name: "",
                address: "",
                city: "",
                state: "",
                zip: "",
                referece_no: "",
                currency: "",
            }),
        };
    },
    methods: {
        gotoCreateBranch() {
            this.$router.push("/app/settings/branch/create");
        },
        async getAllBranches() {
            axios
                .get("/api/branches")
                .then((data) => {
                    this.isLoading = false;
                    this.showNoFound = data.data.data.total == 0 ? true : false
                    this.branches = data.data.data;
                })
                .catch((err) => {
                    console.log(err.message);
                });
        },
        // editBranch(currentBranch) {
        //     console.log("branch", currentBranch.target);
        //     this.$router.push({
        //         name: "edit-branch",
        //         params: {
        //             branchData: currentBranch,
        //             edit: true,
        //         },
        //     });
        // },
    },
    computed: {},
    created() {
        this.getAllBranches();
    },
};
</script>
