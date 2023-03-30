<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">{{!editmode ? "ADD NEW" : "UPDATE" }} BILLER</h6>
                </div>
                <div class="card-body p-0">
                    <form class="position-relative" @submit.prevent="!editmode ? saveBiller() : updateBiller()">
                        <div class="row mt-4">
                            <div class="col-md-12 mb-2">
                                <h4 class="form-banner">BILLER DETAILS</h4>
                            </div>
                            <div class="px-4 py-2 col-md-9">
                                <div class="row g-1 ">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating vs">
                                            <v-select  v-model="form.category" :class="{'is-invalid' : form.errors.has('category')}" :options="billerCategories" placeholder="SELECT CATEGORY" id="company"/>
                                            <label for="company">Biller Category <span>*</span></label>
                                        </div>
                                        <HasError :form="form" field="category" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <FloatingInput v-model="form.name" type="text" label="Biller Name" name="name" :isInvalid="form.errors.has('name')" :required="true" placeholder=""></FloatingInput>
                                        <HasError :form="form" field="name" />
                                    </div>
                                </div>
                                <div class="row g-1 mt-2">
                                    <div class="col-md-12 mb-2">
                                        <small class="mb-2"> <span><i class="bi bi-info-circle-fill text-success"></i></span> Note: These fields are optional if you want to add a biller to a specific company</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating vs">
                                            <v-select  v-model="form.company" @option:selected="getBranchesOnUpdate" :class="{'is-invalid' : form.errors.has('company')}" :options="allCompanies" placeholder="SELECT COMPANY" id="company"/>
                                            <label for="company">Company <span>*</span></label>
                                        </div>
                                        <HasError :form="form" field="company" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating vs">
                                            <v-select  v-model="form.branch" :class="{'is-invalid' : form.errors.has('branch')}" :options="allBranches" placeholder="SELECT BRANCH" id="branch"/>
                                            <label for="company">Branch <span>*</span></label>
                                        </div>
                                        <HasError :form="form" field="branch" />
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating vs">
                                            <v-select  v-model="form.type" :class="{'is-invalid' : form.errors.has('type')}" @option:selected="toogleType" :options="paymentType" placeholder="SELECT TYPE" id="type"/>
                                            <label for="company">Payment Type <span>*</span></label>
                                        </div>
                                        <HasError :form="form" field="type" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 px-4 py-2 animate__animated" v-if="showAmountRange" :class="[showAmountRange ? 'animate__fadeIn' : 'animate__fadeOut']">
                                <div class="row g-1 mb-2">
                                    <div class="col-10 sub-form-banner">
                                        <h5 class="m-0">Amount Ranges</h5>
                                    </div>
                                    <div class="col-2 text-end">
                                        <button @click="addAmountRow($event),amountCounter += 1" type="button" class="btn-card-add"> <i class="bi bi-plus-lg"></i></button>
                                    </div>
                                    <div class="col-12">
                                        <span><HasError :form="form" field="currencies" /></span>
                                    </div>
                                </div>
                                <div  class="row my-2" v-for="(item,index) in form.amount_ranges" :key="index">
                                    <div class="col-10">
                                        <FloatingInput v-model="item.name"  label="Name"  name="name" ></FloatingInput>
                                    </div>
                                    <div class="col-2 text-end">
                                        <button type="button" @click.prevent="removeAmountRow(item,$event)" class="btn-card-minus"> <i class="bi bi-dash-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between mt-5">
                            <div class="col-md-6 mb-2">
                                <h4 class="form-banner">CUSTOM FIELDS</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                    <HasError class="pr-4" :form="form" field="fields" />
                            </div>
                        </div>
                        <div v-for="(itemData,index) in form.fields" :key="index" class="custom-field-items">
                            <div class="row g-1 px-2 py-2" v-if="itemData.type == 'text' || itemData.type == 'number'">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-capitalize">Field {{index+1}} : {{itemData.type}} </h5>
                                    <button @click.prevent="removeRow(itemData,$event)" type="button" class="btn-red p-1 w-150px">REMOVE FIELD</button>
                                </div>
                                <div class="col-md-4" >
                                    <FloatingInput v-model="itemData.label" type="text" label="Enter Label" name="type" :isInvalid="form.errors.has('type')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="fields" />
                                </div>
                                <div class="col-md-2">
                                    <FloatingInput v-model="itemData.width" type="text" label="Enter Text Width" name="type" :isInvalid="form.errors.has('width')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="fields" />
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="half-input-group">Required?</span>
                                        </div>
                                        <select v-model="itemData.require" class="custom-select border-radius-0">
                                            <option value="true">True</option>
                                            <option value="false">False</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-1 px-2 py-2" v-if="itemData.type == 'date'">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-capitalize">Field {{index+1}} : {{itemData.type}} </h5>
                                    <button @click.prevent="removeRow(itemData,$event)"  type="button" class="btn-red p-1 w-150px">REMOVE FIELD</button>
                                </div>
                                <div class="col-md-4" >
                                    <FloatingInput v-model="itemData.label" type="text" label="Enter Label" name="type" :isInvalid="form.errors.has('type')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="fields" />
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="half-input-group">Required?</span>
                                        </div>
                                        <select v-model="itemData.require" class="custom-select border-radius-0">
                                            <option value="true">True</option>
                                            <option value="false">False</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-1 px-2 py-2" v-if="itemData.type == 'select'">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-capitalize">Field {{index+1}} : {{itemData.type}} </h5>
                                    <button @click.prevent="removeRow(itemData,$event)" type="button" class="btn-red p-1 w-150px">REMOVE FIELD</button>
                                </div>
                                <div class="col-md-4" >
                                    <FloatingInput v-model="itemData.label" type="text" label="Enter Label" name="type" :isInvalid="form.errors.has('type')" :required="true" placeholder=""></FloatingInput>
                                    <HasError :form="form" field="fields" />
                                </div>
                                <div class="col-md-4">
                                    <vue-tags-input v-model="itemData.tag" :tags="itemData.options" @tags-changed="newTags => itemData.options = newTags"  />
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="half-input-group">Required?</span>
                                        </div>
                                        <select v-model="itemData.require" class="custom-select border-radius-0">
                                            <option value="true">True</option>
                                            <option value="false">False</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="custom-field-buttons py-2">
                                <div class="form-floating vs w-100">
                                    <v-select v-model="fieldItem" :options="fieldType" placeholder="SELECT FIELD" id="fieldtype"/>
                                    <label for="fieldtype">Field Type <span>*</span></label>
                                </div>
                                <button  @click="addRow($event),counter += 1" type="button" class="btn-red w-50 p-1">ADD FIELD</button>
                            </div>
                        </div>
  
                        <div class="row g-1 justify-content-center mt-3 mb-5">
                            <div class="col-md-2">
                                <button type="button" @click="backPage" class="btn-cancel">Cancel</button>
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
import VueTagsInput from '@sipec/vue3-tags-input';


export default {
    name:"billers-create",
    components:{
        GroupHalfInput,
        GroupHalfDropdown,
        FloatingInput,
        FloatingSelect,
        HasError,
        VueTagsInput
    },
    data(){
        return{
            editmode:false,
            counter:1,
            amountCounter:1,
            fieldItem:'',
            allCompanies:[],
            allBranches:[],
            showAmountRange: false,
            paymentType:[
                {'label': 'One Time Charge', value:'single'},
                {'label': 'Amount Range', value:'range'},
            ],
            fieldType:[
                {'label': 'INPUT TEXT', value:'text'},
                {'label': 'INPUT NUMBER', value:'number'},
                {'label': 'INPUT DATE', value:'date'},
                {'label': 'INPUT SELECT', value:'select'},
            ],
            requiredOptions:{
                'True': 'True',
                'False': 'False',
            },
            billerCategories:[],
            form: new Form({
                id:'',
                category:'',
                name:'',
                company:'',
                branch:'',
                type: {'label': 'One Time Charge', value:'single'},
                amount_ranges:[
                    {id:'', name: ''}
                ],
                fields:[],
            }),
        }
    },
    methods:{
        backPage(){
            this.$router.push('/app/settings/billers')
        }, 
        addRow(event){
                 event.preventDefault()
                    this.form.fields.push({
                        type: this.fieldItem.value,
                        label:'',
                        width:'',
                        require:'true',
                        tag:'',
                        options:[],
                  });
                    
            },
        removeRow(itemData,event){
            this.counter--
            event.preventDefault()
            this.form.fields.splice(this.form.fields.indexOf(itemData),1);
        },
        getBranchesOnUpdate(data){
            axios.get("/api/branches/"+data.value).then(({ data }) => (this.allBranches = data.data));
        },
        addAmountRow(event){
            event.preventDefault()
                    this.form.amount_ranges.push({
                        id:'',
                        name:'',
                  });
        },
        toogleType(data){
            if(data.value == "range"){
                this.showAmountRange =true
            }else{
                this.showAmountRange =false
            }
        },
        removeAmountRow(itemData,event){
            this.amountCounter--
            event.preventDefault()
            this.form.amount_ranges.splice(this.form.amount_ranges.indexOf(itemData),1);
        },
        async saveBiller(){
            this.$Progress.start();
            await axios.get("/sanctum/csrf-cookie");
            this.form
                .post("/api/billers")
                .then((data) => {
                    successMessage('Added!', this.form.name+' has been added.', 'top-right')
                    this.$Progress.finish();
                    this.form.reset();
                    setTimeout(() => {
                       this.backPage()
                    }, 1500); 
                })
                .catch(() => {
                    this.$Progress.fail();
                    errorMessage('Opps!', 'Something went wrong in saving the biller.', 'top-right')
                });
        },
        updateBiller(){
            this.$Progress.start();
            this.form.put("/api/billers/"+this.form.id)
                .then((data) => {
                    successMessage('Updated!', this.form.name+' has been updated.', 'top-right')
                    this.form.reset();
                    this.$Progress.finish();
                    setTimeout(() => {
                       this.backPage()
                    }, 1500); 
                })
                .catch(() => {
                    this.$Progress.fail();
                    errorMessage('Opps!', 'Something went wrong in updating the biller.', 'top-right')
                });
        }
        
    },
    created(){
        axios.get("/api/get-biller-categories").then(({ data }) => (this.billerCategories = data.data));
        axios.get("/api/get-companies").then(({ data }) => (this.allCompanies = data.data));
        if(this.$route.params.data == undefined && this.$route.path == "/app/settings/biller/edit"){
            this.$router.push('/app/settings/billers')
        }
        if(this.$route.params.data != undefined && this.$route.path == "/app/settings/biller/edit"){
            this.editmode = true
            this.form.id = JSON.parse(this.$route.params.data).id
            this.form.name = JSON.parse(this.$route.params.data).name
            this.form.category = {'label': JSON.parse(this.$route.params.data).biller_category_name.name, 'value': JSON.parse(this.$route.params.data).biller_category_id}
            this.form.fields = JSON.parse(JSON.parse(this.$route.params.data).fields)
        }
    }
}
</script>