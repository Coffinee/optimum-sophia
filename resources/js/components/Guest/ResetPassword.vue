<template>
    <div class="container-fluid h-100 login-page">
        <div class="row h-100 align-items-center bg-white">
            <div class="col-md-6 px-md-4 px-sm-2 z-99">
                <router-link to="/">

                    <img class="img-fluid allcash-logo" src="/images/allcash-logo.png" alt="AllCash Logo" />
                </router-link>
                <p>Reset Password</p>
                <form action="javascript:void(0)" method="post">
                    <div class="col-12" v-if="Object.keys(validationErrors).length > 0">
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <li v-for="(value, key) in validationErrors" :key="key">
                                    {{ value[0] }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <GroupButtonLeft v-model="auth.email" type="text" label="USERNAME" icon="bi bi-person" name="email"
                        autocomplete="email" placeholder="ENTER YOUR USERNAME"></GroupButtonLeft>
                    <GroupButtonPassword v-model="auth.password" type="password" label="PASSWORD" icon="bi bi-lock"
                        name="password" autocomplete="email" placeholder="ENTER YOUR PASSWORD"></GroupButtonPassword>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1" />
                                <label class="form-check-label text-blue" for="exampleCheck1">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-sm-6 text-end">
                            <div class="mb-3">
                                <router-link class="text-blue" to="/">Forgot Password?</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="py-4">
                        <div class="captcha-bg input-group mb-3">
                            <input type="text" class="form-control w-50 rounded-0 shadow-none"
                                placeholder="ENTER CODE HERE" />
                            <span class="input-group-text w-50 rounded-0 captcha"
                                style="background: #A0A0A0 !important; padding: 8.6px; margin: auto; width: 50%;">
                                {{ generated_code }}
                            </span>
                        </div>
                    </div>
                    <button :disabled="processing" @click="login" class="btn btn-lg btn-login draw-border" type="">
                        {{ processing ? "Please wait" : "LOGIN" }}
                    </button>
                </form>
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
import GroupButtonPassword from "@/components/Misc/Inputs/GroupButtonPassword.vue";

export default {
    name: "resetpassword",
    components: {
        GroupButtonLeft,
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
