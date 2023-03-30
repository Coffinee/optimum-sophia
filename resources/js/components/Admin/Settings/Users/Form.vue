<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">{{!editmode ? "ADD NEW" : "UPDATE" }} USER</h6>
                </div>
                <div class="card-body p-0">
                    <form @submit.prevent="!editmode ? saveUser() : updateUser()">
                        <div class="row p-3 justify-content-between">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center ">
                                    <div class="company-logo" :style="{'background-image': `url(${previewImage})`,}" ></div>
                                <input ref="fileUserPix" class="inputfile my-2" type="file" @input="pickFile"/>
                                <label for="file"  @click="$refs.fileUserPix.click()"><i class="bi bi-upload mr-2"></i> UPLOAD</label>
                                
                                </div>
                                <HasError :form="form" field="profile_picture" class="text-end" />
                            </div>
                            <div class="col-md-4 align-items-center">
                                <GroupHalfDropdown v-model="form.status" label="STATUS" :options="statusOptions" :selected="'1'" name="company-name" :isInvalid="form.errors.has('status')"></GroupHalfDropdown>
                                <GroupHalfInput v-model="form.user_name" label="USERNAME" type="text" :isDisabled="true" name="company-name" :isInvalid="form.errors.has('user_name')"></GroupHalfInput>
                                <HasError :form="form" field="user_name" class="text-end" />
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12 mb-2">
                                <h4 class="form-banner">USER DETAILS</h4>
                            </div>
                            <div class="px-4 py-2 col-md-12">
                                <div class="row g-1 ">
                                    <div class="col-lg-2 mb-3">
                                        <FloatingInput v-model="form.last_name" label="Last Name" name="lastName" :isInvalid="form.errors.has('last_name')" :required="true" placeholder=""></FloatingInput>
                                        <HasError :form="form" field="last_name" />
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <FloatingInput v-model="form.first_name" label="First Name" name="firstName" :isInvalid="form.errors.has('first_name')" :required="true" placeholder=""></FloatingInput>
                                        <HasError :form="form" field="first_name" />
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <FloatingInput v-model="form.middle_name" label="Middle Name" name="middleName"  placeholder=""></FloatingInput>
                                    </div>
                                    <div class="col-lg-1 mb-3">
                                        <FloatingInput v-model="form.suffix" label="Suffix" name="floatingInput"  placeholder=""></FloatingInput>
                                    </div>
                                    <div class="col-lg-2 mb-3">
                                        <FloatingInput v-model="form.mobile_number" label="Mobile Number" name="mobileNumber" :isInvalid="form.errors.has('mobile_number')" :required="true" placeholder=""></FloatingInput>
                                        <HasError :form="form" field="mobile_number" />
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <FloatingInput v-model="form.email" type="email" label="Email" name="userEmail" :uppercased="false" :isInvalid="form.errors.has('email')" :required="true" placeholder=""></FloatingInput>
                                        <HasError :form="form" field="email" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12 mb-2">
                                <h4 class="form-banner">COMPANY DETAILS</h4>
                            </div>
                            <div class="px-4 py-2 col-md-12">
                                <div class="row g-1">
                                    <div class="col-lg-4 mb-3">
                                        <div class="form-floating vs">
                                            <v-select  v-model="form.company" @option:selected="getBranches" :class="{'is-invalid' : form.errors.has('company_id')}" :options="allCompanies" placeholder="SELECT COMPANY" id="company"/>
                                            <label for="company">Company <span>*</span></label>
                                        </div>
                                        <HasError :form="form" field="company_id" />
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <div class="form-floating vs">
                                            <v-select  v-model="form.branch" :options="allBranches" :class="{'is-invalid' : form.errors.has('branch_id')}" placeholder="SELECT BRANCH" id="branch"/>
                                            <label for="branch">Branch <span>*</span></label>
                                        </div>
                                        <HasError :form="form" field="branch_id" />
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <div class="form-floating vs">
                                            <v-select  v-model="form.role" :options="roleOptions" :class="{'is-invalid' : form.errors.has('branch_id')}" placeholder="SELECT ROLE" id="role"/>
                                            <label for="role">Role <span>*</span></label>
                                        </div>
                                        <HasError :form="form" field="branch_id" />
                                        <!-- <FloatingSelect v-model="form.role" :options="roleOptions" label="Role" placeholder="Select Role" name="role"></FloatingSelect> -->
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12 mb-2">
                                <h4 class="form-banner">MODULE ACCESS</h4>
                                <HasError class="pl-2" :form="form" field="access" />
                            </div>
                            <div class="px-4 py-2 col-md-12">
                                <div class="row g-1 py-2">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input v-model="form.access" class="form-check-input" id="createTransaction" type="checkbox" value="create_transaction" >
                                            <label class="form-check-label" for="createTransaction">
                                                Create Transaction
                                            </label>
                                        </div>
                                        <div class="pl-4">
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="fileUpload" type="checkbox" value="file_upload" >
                                                <label class="form-check-label" for="fileUpload">
                                                    File Upload
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="dataEntry" type="checkbox" value="data_entry" >
                                                <label class="form-check-label" for="dataEntry">
                                                    Data Entry
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input v-model="form.access" class="form-check-input" id="amendment" type="checkbox" value="amendment" >
                                            <label class="form-check-label" for="amendment">
                                                AMENDMENT
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input v-model="form.access" class="form-check-input" id="inquiry" type="checkbox" value="inquiry" >
                                            <label class="form-check-label" for="inquiry">
                                                INQUIRY
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input v-model="form.access" class="form-check-input" id="transactions" type="checkbox" value="transactions" >
                                            <label class="form-check-label" for="transactions">
                                                TRANSACTIONS
                                            </label>
                                        </div>
                                        <div class="pl-4">
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="monitorTransactions" type="checkbox" value="monitor_transactions" >
                                                <label class="form-check-label" for="monitorTransactions">
                                                    Monitor Transactions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="pendingTransactions" type="checkbox" value="pending_transactions" >
                                                <label class="form-check-label" for="pendingTransactions">
                                                    Pending Transactions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="processedTransactions" type="checkbox" value="proccessed_transactions" >
                                                <label class="form-check-label" for="processedTransactions">
                                                    Proccessed Transactions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="refundRequest" type="checkbox" value="refund_request" >
                                                <label class="form-check-label" for="refundRequest">
                                                    Refund Request
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input v-model="form.access" class="form-check-input" id="reports" type="checkbox" value="reports" >
                                            <label class="form-check-label" for="reports">
                                                REPORTS
                                            </label>
                                        </div>
                                        <div class="pl-4">
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="dispositionReport" type="checkbox" value="disposition_report" >
                                                <label class="form-check-label" for="dispositionReport">
                                                    Disposition Report
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="longOutstandingReport" type="checkbox" value="long_outstanding_report" >
                                                <label class="form-check-label" for="longOutstandingReport">
                                                    Long Outstanding Report
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="statusReport" type="checkbox" value="status_report" >
                                                <label class="form-check-label" for="statusReport">
                                                    Status Report
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input v-model="form.access" class="form-check-input" id="settings" type="checkbox" value="settings" >
                                            <label class="form-check-label" for="settings">
                                                SETTINGS
                                            </label>
                                        </div>
                                        <div class="pl-4">
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="rate" type="checkbox" value="rate_management" >
                                                <label class="form-check-label" for="rate">
                                                    Rate Management
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="fees" type="checkbox" value="fees_management" >
                                                <label class="form-check-label" for="fees">
                                                    Fees Management
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="company" type="checkbox" value="company" >
                                                <label class="form-check-label" for="company">
                                                    Company
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="branch" type="checkbox" value="branch" >
                                                <label class="form-check-label" for="branch">
                                                    Branch
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="users" type="checkbox" value="users" >
                                                <label class="form-check-label" for="users">
                                                    Users
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="billers" type="checkbox" value="billers" >
                                                <label class="form-check-label" for="billers">
                                                    Billers
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input v-model="form.access" class="form-check-input" id="others" type="checkbox" value="others" >
                                                <label class="form-check-label" for="others">
                                                    Others
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input v-model="form.access" class="form-check-input" id="help" type="checkbox" value="help" >
                                            <label class="form-check-label" for="help">
                                                HELP
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 justify-content-center mt-3 mb-5">
                            <div class="col-md-2">
                                <button type="button" @click="backToUserPage" class="btn-cancel">Cancel</button>
                            </div>
                            <div class="col-md-2">
                                <button :disabled="form.busy" type="submit" class="btn-save">{{!editmode ? "SAVE" : "UPDATE"}} </button>
                            </div>
                        </div>
                    </form>
                
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from "vform";
import { HasError } from "vform/src/components/bootstrap5";
import { errorMessage, successMessage } from '@/utilities.js'
import GroupHalfInput from "@/components/Misc/Inputs/GroupHalfInput.vue";
import GroupHalfDropdown from "@/components/Misc/Inputs/GroupHalfDropdown.vue";
import FloatingInput from "@/components/Misc/Inputs/FloatingInput.vue";
import FloatingSelect from "@/components/Misc/Inputs/FloatingSelect.vue";


export default {
    name:"create-user",
    components:{
        GroupHalfInput,
        GroupHalfDropdown,
        FloatingInput,
        FloatingSelect,
        HasError
    },
    data(){
        return{
            editmode:false,
            allCompanies:[],
            allBranches:[],
            previewImage: '/images/upload-icon.png',
            statusOptions:{
                "0": "Inactive",
                "1": "Active",
            },
            token:'',
            companyOptions:[],
            branchOptions:[],
            roleOptions:
            [
                {label: 'Admin', value: 'admin'},
                {label: 'Approver', value: 'approver'},
                {label: 'And/Or', value: 'andor'},
                {label: 'Centralized', value: 'centralized'},
                {label: 'Maker', value: 'maker'},
                {label: 'Verifier', value: 'verifier'},
            ],
            form: new Form({
                id:'',
                user_permission_id:'',
                profile_picture:"",
                status:'1',
                user_name:'',
                last_name:"",
                first_name:"",
                middle_name:"",
                suffix:"",
                mobile_number:"",
                email:"",
                company:"",
                branch:"",
                role:"",
                access:[],
            }),
        }
    },
    methods:{
        backToUserPage(){
            this.$router.push('/app/settings/users')
        },
        setSelected(value){
            console.log(value)
        },
        pickFile() {
            let input = this.$refs.fileUserPix;
            let file = input.files;
            if (file && file[0]) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                    this.form.profile_picture = e.target.result;
                };
                reader.readAsDataURL(file[0]);
                this.$emit("input", file[0]);
            }
        },
       async getBranches(data){
        console.log(data.value)
            this.form.user_name = this.createUserName(data.label,this.form.first_name.toUpperCase())
            await axios.get(this.$eosUrl+'/api/outbound/get-branches',{
                params:{
                    company_id : data.value
                },
                'headers': { 'Authorization': 'Bearer '+ this.token }
            }).then((data) =>{
                this.allBranches = data.data.data;
            }).catch((e) => {
                errorMessage('Opps!', e.message, 'top-right')
            });
        },
        getBranchesOnUpdate(id){
            axios.get("/api/branches/"+id).then(({ data }) => (this.allBranches = data.data));
        },
        createUserName(cname, fname){
            if(fname =="") return errorMessage('Opps!', 'Enter first name to generate a username.', 'top-right')
            const username = cname
            .toUpperCase()
            .split(' ')
            .map((elem) => elem[0])
            .join('');
            return username+'_'+fname;
        },  
        async saveUser(){
            this.$Progress.start();
            await axios.get("/sanctum/csrf-cookie");
            this.form
                .post("/api/users")
                .then((data) => {
                    this.previewImage = '/images/upload-icon.png'
                    successMessage('Saved!',this.form.first_name+' has been added.','top-right')
                    this.form.reset();
                    this.$Progress.finish();
                    setTimeout(() => {
                       this.backToUserPage()
                    }, 1500); 
                })
                .catch(() => {
                    this.$Progress.fail();
                    errorMessage('Opps!', 'Something went wrong in saving the user.', 'top-right')
                });
        },
        updateUser(){
            this.$Progress.start();
            this.form.put("/api/users/"+this.form.id)
                .then((data) => {
                    successMessage('Updated!',this.form.first_name+' has been updated.','top-right')
                    this.form.reset();
                    this.previewImage = '/images/upload-icon.png'
                    this.$Progress.finish();
                    setTimeout(() => {
                       this.backToUserPage()
                    }, 1500); 
                })
                .catch(() => {
                    this.$Progress.fail();
                    errorMessage('Opps!', 'Something went wrong in updating the user.', 'top-right')
                });
        }
        
    },
    created(){
        axios.get('/api/get-inbound-api-token').then((data)=> { this.token = data.data.token }).finally(async (e)=> { 

            await axios.get(this.$eosUrl+'/api/outbound/get-companies', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                this.allCompanies = data.data.data;
            }).catch((e) => {
                errorMessage('Opps!', e.message, 'top-right')
            });

         });

        if(this.$route.params.data == undefined && this.$route.path == "/app/settings/user/edit"){
            this.$router.push('/app/settings/users')
        }
        if(this.$route.params.data != undefined && this.$route.path == "/app/settings/user/edit"){
            this.editmode = true
            this.form.fill(JSON.parse(this.$route.params.data))
            this.form.role = {'label': JSON.parse(this.$route.params.data).role, 'value': JSON.parse(this.$route.params.data).role}
            this.form.company =  {'label': JSON.parse(this.$route.params.data).company_name.name, 'value': JSON.parse(this.$route.params.data).company_name.id} 
            this.getBranchesOnUpdate(JSON.parse(this.$route.params.data).company_name.id)
            this.form.branch = {'label': JSON.parse(this.$route.params.data).branch_name.name, 'value': JSON.parse(this.$route.params.data).branch_name.id} 
            this.previewImage = '/uploads/200x200/'+JSON.parse(this.$route.params.data).profile_picture
            this.form.access = JSON.parse(this.$route.params.data).permissions.permissions
            this.form.user_permission_id = JSON.parse(this.$route.params.data).permissions.id
        }
    }
}
</script>