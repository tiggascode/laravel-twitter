<script setup>
import {ArrowDownTrayIcon, PaperClipIcon} from '@heroicons/vue/20/solid'
import {isImage, isVideo} from "@/helpers.js";

defineProps({
    attachments: Array,

})

defineEmits(['attachmentClick'])
</script>

<template>
    <template v-for="(attachment,ind) of attachments.slice(0,4)">
        <div class="group  bg-blue-100 flex flex-col items-center
                        justify-center text-gray-500 relative cursor-pointer"
             @click="$emit('attachmentClick',ind)">

            <div v-if="ind === 3 && attachments.length > 4" class="absolute left-0 top-0
                           right-0 bottom-0 z-10 bg-black/60 text-white flex
                           items-center justify-center text-2xl">
                +{{ attachments.length - 4 }} more
            </div>


            <!--Download-->
            <a :href="route('post.download', attachment)" class="z-20 opacity-0 group-hover:opacity-100  transition-all w-8 h-8 flex items-center justify-center text-gray-100
                        bg-gray-600 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800"
               @click.stop>
                <ArrowDownTrayIcon class="w-4 h-4 "/>
            </a>
            <!--Download-->

            <img v-if="isImage(attachment)" :src="attachment.url"
                 class="object-contain">
            <div v-else-if="isVideo(attachment)" class="relative flex justify-center items-center object-square">
                <svg class="z-20 absolute w-16 h-16 text-gray-100 " fill="none" stroke="currentColor"
                     stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round"/>
                    <path
                        d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112Z"
                        stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
                <div class="absolute left-0 right-0 top-0 w-full bg-black/50 z-10"></div>
                <video :src="attachment.url" controls></video>
            </div>
            <div v-else class="flex flex-col justify-center items-center">
                <PaperClipIcon class="w-10 h-10 mb-3"/>

                <small>{{ attachment.name }}</small>
            </div>

        </div>
    </template>

</template>

<style scoped>

</style>
