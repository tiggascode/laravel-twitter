<script setup>

import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";

const searchKeyword = ref('');

</script>

<template>
    <TextInput :model-value="searchKeyword" class="w-full mt-3" placeholder="Type To search "/>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400   text-center p-3">
            You dont have friends yet.
        </div>
        <div v-else>


        </div>
    </div>
</template>

<style scoped>

</style>
