<template>
    
    <table class="table">
        <thead class="table-header-bg">
            <tr>
                <th v-for="th in tableHeader" :key="th" scope="col">
                    <div   class="d-flex justify-content-between">
                        <span>{{ th.text }}</span>
                        <span><i class="bi bi-sort-down-alt"></i></span>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="td in tableData" :key="td">
                <td >{{td.id}}</td>
                <td >{{td.id}}</td>
                <td>{{td.name}}</td>
                <td >{{td.code}}</td>
                <td>{{td.created_at}}</td>
                <td></td>s
            </tr>
        </tbody>
    </table>

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
</template>

<script>
export default{
    name:"TableBase",
    props:{
        columns:Array,
        entries: Array
    },
    computed:{
        tableHeader(){
            return this.columns || []
        },
        tableData(){
            return this.entries || []
        }
    }
}
</script>