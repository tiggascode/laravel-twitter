<script setup>
import {ArrowDownTrayIcon, PaperClipIcon} from '@heroicons/vue/20/solid'

import {isImage} from "@/helpers.js";
import {ref} from "vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";

const showModal = ref(false);
const currentPhotoIndex = ref(0);
defineProps({
    photos: Array
})

function openPhoto(index) {
    currentPhotoIndex.value = index
    showModal.value = true;
}
</script>

<template>
    <div class="grid gap-2 grid-cols-2 sm:grid-cols-3">
        <template v-for="(attachment,ind) of photos">
            <div class="group  bg-blue-100 flex flex-col items-center
                        justify-center text-gray-500 relative cursor-pointer"
                 @click="openPhoto(ind)">


                <!--Download-->
                <a :href="route('post.download', attachment)" class="z-20 opacity-0 group-hover:opacity-100  transition-all w-8 h-8 flex items-center justify-center text-gray-100
                        bg-gray-600 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800"
                   @click.stop>
                    <ArrowDownTrayIcon class="w-4 h-4 "/>
                </a>
                <!--Download-->

                <img v-if="isImage(attachment)" :src="attachment.url"
                     class="object-contain">
                <div v-else class="flex flex-col justify-center items-center">
                    <PaperClipIcon class="w-10 h-10 mb-3"/>

                    <small>{{ attachment.name }}</small>
                </div>

            </div>
        </template>
    </div>

    <AttachmentPreviewModal v-model="showModal"
                            v-model:index="currentPhotoIndex"
                            :attachments="photos || []"/>
</template>

<style scoped>

</style>
