<template>
    <div class="container-fluid">
        <div class="h-100">
            <div class="main-title">
                <p class="subtitle-hi">HI!</p>
                <p class="subtitle">WELCOME TO</p>
                <img class="sophia-logo" src="/images/sophia-logo.png" alt="Sophia Logo">
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

<script setup>
import { loadFull } from "tsparticles";

const particlesInit = async (engine) => {
    await loadFull(engine);
}

const particlesLoaded = async (container) => {
    console.log("Particles container loaded", container);
}

</script>
