<script setup>
import {computed} from 'vue'
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot,} from '@headlessui/vue'
import {ChevronLeftIcon, ChevronRightIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import {isImage} from "@/helpers.js";
import {PaperClipIcon} from "@heroicons/vue/20/solid/index.js";


const props = defineProps({
    attachments: {
        type: Array,
        required: true
    },
    index: Number,
    modelValue: Boolean,
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const currentIndex = computed({
    get: () => props.index,
    set: (value) => emit('update:index', value)
})

const attachment = computed(() => {
    return props.attachments[currentIndex.value]
})


const emit = defineEmits(['update:modelValue', 'update:index', 'hide'])


function closeModal() {
    show.value = false
    emit('hide')
}

function prev() {
    if (currentIndex.value === 0) return
    currentIndex.value--
}

function next() {
    if (currentIndex.value === props.attachments.length - 1) return
    currentIndex.value++
}
</script>


<template>
    <teleport to="body">
        <TransitionRoot :show="show" appear as="template">
            <Dialog as="div" class="relative z-50" @close="closeModal">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25"/>
                </TransitionChild>

                <div class="fixed left-0 top-0 bottom-0 right-0 inset-0 overflow-y-auto">
                    <div
                        class="  h-screen w-screen "
                    >
                        <TransitionChild
                            as="template"
                            class="w-full h-full"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="flex flex-col w-full  transform overflow-hidden  bg-slate-800
                                 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="flex items-center justify-between py-3 px-4  font-medium bg-slate-800 text-gray-900"
                                >
                                    <button class="absolute right-3 top-3 z-30 w-8 h-8 rounded-full
                                        hover:bg-black/10 transition flex items-center
                                            justify-center font-bold text-gray-100" @click="closeModal">
                                        <XMarkIcon class="w-6 h-6 "/>
                                    </button>
                                </DialogTitle>
                                <div class="  relative  group h-full bg-slate-800">
                                    <div class="absolute opacity-0 group-hover:opacity-100 text-gray-100
                                        cursor-pointer flex items-center justify-center w-12 h-full left-0 bg-black/5"
                                         @click="prev"
                                    >
                                        <ChevronLeftIcon class="w-12"/>
                                    </div>
                                    <div class="absolute opacity-0 group-hover:opacity-100 text-gray-100
                                        cursor-pointer flex items-center justify-center w-12 h-full right-0 bg-black/5"
                                         @click="next"
                                    >
                                        <ChevronRightIcon class="w-12"/>
                                    </div>


                                    <div class="flex items-center justify-center w-full h-full p-3">
                                        <img v-if="isImage(attachment)" :src="attachment.url"
                                             class=" max-w-full max-h-full">
                                        <div v-else class="p-32 flex flex-col justify-center
                                            items-center text-gray-100">
                                            <PaperClipIcon class="w-10 h-10 mb-3"/>

                                            <small>{{ attachment.name }}</small>
                                        </div>
                                    </div>
                                </div>

                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
