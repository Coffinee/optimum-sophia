<template>
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">COMPANY</h6>
                </div>
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-7">
                                    <!-- <Search v-model="searchInput"  icon="bi-search" name="search" placeholder="Search"></Search> -->
                                    <div class="form-group has-search">
                                        <i class="bi bi-search form-control-feedback"></i>
                                        <input v-model="searchInput" @keyup="searchEvent()" type="text" name="search" class="form-control" placeholder="Search">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <ButtonBlack icon="bi bi-download" label="Export"></ButtonBlack>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <ButtonOrange icon="bi bi-plus-circle" label="Add Company"></ButtonOrange>
                        </div>
                    </div>
                    <div class="col-md-12 shadow">
                        <TableBase :columns="columns" :entries="filteredEntries"></TableBase>
                        <div class="row mt-2 py-3">
                            <div class="col">
                                <span class="mr-1">Show</span>
                                <select class="select" name="show-entries" id="" v-model="currentEntries" @change="paginateEntries">
                                    <option v-for="entry in showEntries" :key="entry" :value="entry">{{ entry }}</option>
                                </select>
                                <span class="ml-1">entries</span>
                            </div>
                            <div class="col text-center">
                                <span>Showing {{showTableInfo.from}} to {{showTableInfo.to}} of {{showTableInfo.of}} entries</span>
                            </div>
                            <div class="col">
                                <nav>
                                    <ul class="pagination justify-content-end">
                                        <li class="page-item" :class="{'disabled' : currentPage === 1}">
                                            <a class="page-link" href="#" tabindex="-1" @click.prevent="(currentPage < 1) ? currentPage = 1 : currentPage -= 1, paginateEntries()">Prev</a>
                                        </li>
                                        <li v-for="page in showPagination" :key="page" class="page-item" :class="{'active': page === currentPage}">
                                            <a class="page-link" href="#" @click.prevent="paginateEvent(page)">{{page}}</a>
                                        </li>
                                        <li class="page-item" :class="{'disabled' : currentPage === allPages}">
                                            <a class="page-link" href="#" tabindex="-1" @click.prevent="(currentPage < allPages) ? currentPage = allPages : currentPage += allPages, paginateEntries()">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div> 
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { $array } from "alga-js";
import Search from "@/components/Misc/Inputs/Search.vue";
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import TableBase from "@/components/Misc/Table/TableBase.vue";

export default {
    name:"company",
    components:{
        Search,
        ButtonOrange,
        ButtonBlack,
        TableBase
    },
    data(){
        return {
            user:this.$store.state.auth.user,
            columns:[
                    { name:"check" , text: 'ID' },
                    { name:"id" , text: "COMPANY ID" },
                    { name:"name" , text: "COMPANY NAME" },
                    { name:"code" , text: "PARTNER CODE" },
                    { name:"date_created" , text: "DATE ADDED" },
                    { name:"actions" , text: "ACTIONS" },
                ],
                entries: [],
                showEntries: [5,10, 25, 50, 100],
                currentEntries: 5,
                filteredEntries:[],
                currentPage: 1,
                allPages:1,
                searchInput:'',
                searchEntries:[]
        }
    },
    methods:{
        paginateEntries(){
            if(this.searchInput.length >= 3){
                this.searchEntries = $array.search(this.entries, this.searchInput)
                this.filteredEntries = $array.paginate(this.searchEntries, this.currentPage, this.currentEntries)
                this.allPages = $array.pages(this.searchEntries, this.currentEntries)
            }else{
                this.searchEntries = []
                this.filteredEntries = $array.paginate(this.entries, this.currentPage, this.currentEntries)
                this.allPages = $array.pages(this.entries, this.currentEntries)
            }
            
        },
        async getAllCompanies(){
            const response = await fetch('http://127.0.0.1:8000/api/companies')
            return response.json()
        },
        paginateEvent(page){
            this.currentPage = page
            this.paginateEntries()
        },
        searchEvent(){
            this.currentPage = 1
            this.paginateEntries()
        }
    },
    computed:{
        showTableInfo(){
            const getCurrentEntries = (this.searchEntries.length <= 0) ? this.entries : this.searchEntries
            return $array.show(getCurrentEntries, this.currentPage, this.currentEntries)
        },
        showPagination(){
            return $array.pagination(this.allPages, this.currentPage, 3)
        },
       
    },  
    created(){
        this.getAllCompanies().then(res => {
                this.entries = res.data
                this.filteredEntries = $array.paginate(this.entries, this.currentPage, this.currentEntries)
                this.allPages = $array.pages(this.entries, this.currentEntries)
           })
    }
}
</script>