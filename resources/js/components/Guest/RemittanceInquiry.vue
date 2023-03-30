<template>
    <div class="container-fluid">
        <div class="h-100">
            <div class="main-remittance">
                <div class="remittance-logo">
                    <img class="sophia-logo" src="/images/sophia-logo.png" alt="Sophia Logo">
                </div>
            <div class="remittance-subtitle">
                <p><span class ="remittance-subtitle-know">Know the status of your remittance now!</span>
                <br/> <br/>
                <span class="remittance-subtitle-simple">Simply enter the complete reference number including alphabet or
                        special characters (if any) provided by remittance partners of your remitter.</span></p>
            </div>
        <div class="container remittance-inquiry-form" >
            <div class="row remittance-ref">
                <GroupButtonRight type="text" icon="bi bi-search" name="reference-input"
                autocomplete="" placeholder="ENTER REMITTANCE REFERENCE NO."></GroupButtonRight>
            </div>
                <div class="container">
                    <div class="captcha-bg input-group mb-3 col-10">
                        <span class="input-group-text w-100 rounded-0 captcha bg-opacity-1"
                        style="background: #FEEAD2 !important; padding: 8.6px; margin: auto; width: 100%;">
                        {{ generated_code }}
                        </span>
                    </div>
                    <div class="col-2">
                        <button class="row"><i class="bi bi-volume-up"></i></button>
                        <button class="row"><i class="bi bi-arrow-repeat"></i></button>
                    </div>
                </div>
            <div class="row remittance-ref">
                <GroupButtonRight type="text" icon="bi bi-search" name="captcha-input"
                placeholder="TYPE THE CHARACTERS ABOVE"></GroupButtonRight>
            </div>
        </div>
           </div>
                <div class="row text-center home-links">
                    <div class="col">
                        <div class="py-2">
                            <router-link to="/remittance-inquiry" class="px-2 m-0 fs-5 sm-fs-4"><i class="bi bi-info-circle fs-5"></i> REMITTANCE INQUIRY</router-link>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                         <div>Powered by</div>
                            <div>
                                <img class="allcash-logo" src="/images/allcash.png" alt="All Cash Logo" />
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="py-2">
                            <router-link to="/login" class="px-2 m-0 fs-5">LOGIN NOW <i class="bi bi-arrow-right-circle fs-5"></i></router-link>
                         </div>
                    </div>
                </div>
             </div>
    </div>
</template>

<script>
import { loadFull } from "tsparticles";
import GroupButtonRight from "@/components/Misc/Inputs/GroupButtonRight.vue";


const particlesInit = async (engine) => {
    await loadFull(engine);
}

const particlesLoaded = async (container) => {
    console.log("Particles container loaded", container);
}

export default{

    name: 'RemittanceInquiry',

    components: {
        GroupButtonRight ,
    },

    data() {
        return{
        generated_code: '',
        }
    },

    methods: {
        generateCaptcha(length) {
            let word = "";
            const alphabet = 'abcdefhijklmnopqrstuvwxyz';

            for (let i = 0; i < length; i++) {
                word += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
            }
            return this.generated_code = word;
        },
    },

    mounted() {
        this.generateCaptcha(6);
    }
}


</script>
