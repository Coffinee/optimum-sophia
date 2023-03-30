<template>
    <div class="container-fluid h-100 login-page">
        <div class="row h-100 align-items-center bg-white">
            <div class="col-md-6 px-md-4 px-sm-2 z-99">
                <router-link to="/">
                    <img class="img-fluid sophia-login-logo" src="/images/sophia-reset-password-logo.png" alt="AllCash Logo" />
                </router-link>
                <form action="javascript:void(0)" method="post">
                    <!-- <div class="col-12" v-if="Object.keys(validationErrors).length > 0">
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <li v-for="(value, key) in validationErrors" :key="key">
                                    {{ value[0] }}
                                </li>
                            </ul>
                        </div>
                    </div> -->
                    <div class="container height-100 d-flex justify-content-center align-items-center">
                        <div class="position-relative">
                            <div class="p-2 text-center">
                                <h6 class="">Enter below the Reference Code that we sent to your email.</h6>
                                <div class="otp-inputs d-flex flex-row justify-content-center mt-2">
                                    <input class="m-2 text-center form-control rounded-0" type="text" maxlength="1" /> 
                                    <input class="m-2 text-center form-control rounded-0" type="text" maxlength="1" /> 
                                    <input class="m-2 text-center form-control rounded-0" type="text" maxlength="1" /> 
                                    <input class="m-2 text-center form-control rounded-0" type="text" maxlength="1" />
                                    <input class="m-2 text-center form-control rounded-0" type="text" maxlength="1" /> 
                                    <input class="m-2 text-center form-control rounded-0" type="text" maxlength="1" /> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <GroupButtonRight v-model="auth.password" type="password" label="NEW PASSWORD" icon="bi bi-eye"
                        name="password" autocomplete="password" placeholder="ENTER YOUR NEW PASSWORD"></GroupButtonRight>
                    <GroupButtonRight v-model="auth.password" type="password" label="CONFIRM PASSWORD" icon="bi bi-eye"
                        name="password" autocomplete="empasswordail" placeholder="RE-ENTER YOUR NEW PASSWORD"></GroupButtonRight>

                    <!-- PASSWORD CRITERIA 
                        <div class="row">

                    </div>  -->
                    <button :disabled="processing" @click="login" class="btn btn-lg btn-login draw-border" type="button">
                        {{ processing ? "Please wait" : "LOGIN" }}
                    </button>
                </form>
                <div class="text-center">
                    <h1 class="powered-by">Powered by</h1>
                    <div class="all-cash">
                        <img class="allcash-logo" src="/images/allcash.png" alt="" />
                    </div>
                </div>
            </div>
            <div class="col-md-6 r-side h-100">
                <Particles id="tsparticles" :particlesInit="particlesInit" :particlesLoaded="particlesLoaded" :options="{
                    particles: {
                        number: {
                            value: 60,
                            density: {
                                enable: true,
                                value_area: 800,
                            },
                        },
                        color: {
                            value: '#ffffff',
                        },
                        shape: {
                            type: 'circle',
                            stroke: {
                                width: 0,
                                color: '#000000',
                            },
                            polygon: {
                                nb_sides: 5,
                            },
                            image: {
                                src: 'img/github.svg',
                                width: 100,
                                height: 100,
                            },
                        },
                        opacity: {
                            value: 0.5,
                            random: false,
                            anim: {
                                enable: false,
                                speed: 1,
                                opacity_min: 0.1,
                                sync: false,
                            },
                        },
                        size: {
                            value: 3,
                            random: true,
                            anim: {
                                enable: false,
                                speed: 40,
                                size_min: 0.1,
                                sync: false,
                            },
                        },
                        line_linked: {
                            enable: true,
                            distance: 250,
                            color: '#ffffff',
                            opacity: 0.4,
                            width: 1.7361522131994906,
                        },
                        move: {
                            enable: true,
                            speed: 1,
                            direction: 'none',
                            random: false,
                            straight: false,
                            out_mode: 'out',
                            bounce: false,
                            attract: {
                                enable: false,
                                rotateX: 600,
                                rotateY: 1200,
                            },
                        },
                    },
                    interactivity: {
                        detect_on: 'canvas',
                        events: {
                            onhover: {
                                enable: false,
                                mode: 'bubble',
                            },
                            onclick: {
                                enable: true,
                                mode: 'push',
                            },
                            resize: true,
                        },
                        modes: {
                            grab: {
                                distance: 400,
                                line_linked: {
                                    opacity: 1,
                                },
                            },
                            bubble: {
                                distance: 400,
                                size: 40,
                                duration: 2,
                                opacity: 8,
                                speed: 3,
                            },
                            repulse: {
                                distance: 200,
                                duration: 0.4,
                            },
                            push: {
                                particles_nb: 4,
                            },
                            remove: {
                                particles_nb: 2,
                            },
                        },
                    },
                    retina_detect: true,
                }" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { loadFull } from "tsparticles";
const particlesInit = async (engine) => {
    await loadFull(engine);
};

const particlesLoaded = async (container) => {
    console.log("Particles container loaded", container);
};
</script>

<script>
import { mapActions } from "vuex";
import GroupButtonLeft from "@/components/Misc/Inputs/GroupButtonLeft.vue";
import GroupButtonRight from "@/components/Misc/Inputs/GroupButtonRight.vue";
import GroupButtonPassword from "@/components/Misc/Inputs/GroupButtonPassword.vue";

export default {
    name: "login",
    components: {
        GroupButtonLeft, GroupButtonRight, GroupButtonPassword, 
    },
    data() {
        return {
            auth: {
                email: "",
                password: "",
            },
            validationErrors: {},
            processing: false,
            generated_code: '',
        };
    },
    methods: {
        ...mapActions({
            signIn: "auth/login",
        }),
        generateCaptcha(length) {
            let word = "";
            const alphabet = 'abcdefhijklmnopqrstuvwxyz';

            for (let i = 0; i < length; i++) {
                word += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
            }
            return this.generated_code = word;
        },
        async login() {
            this.processing = true;            
            await axios.get("/sanctum/csrf-cookie");
            await axios
                .post("/login", this.auth)
                .then(({ data }) => {
                    this.signIn();
                })
                .catch(({ response }) => {
                    if (response.status === 422) {
                        this.validationErrors = response.data.errors;
                    } else {
                        this.validationErrors = {};
                        alert(response.data.message);
                    }
                })
                .finally(() => {
                    this.processing = false;
                });
        },
    },
    mounted() {
        this.generateCaptcha(6);
    }
};
</script>
