<script setup>
import {computed, ref, watch} from 'vue'
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot,} from '@headlessui/vue'
import PostUserHeader from "@/Components/app/PostUserHeader.vue";
import {ArrowUturnLeftIcon, BookmarkIcon, PaperClipIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import {useForm, usePage} from "@inertiajs/vue3";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import {isImage} from "@/helpers.js";


const editor = ClassicEditor;
const editorConfig = {
    toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|',
        'blockQuote'],
}

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    group: {
        type: Object,
        default: null
    },

    modelValue: Boolean,
})

const attachmentExtensions = usePage().props.attachmentExtensions

/**
 * {
 *     file: File,
 *     url: '',
 * }
 * @type {Ref<UnwrapRef<*[]>, UnwrapRef<*[]> | *[]>}
 */
const attachmentFiles = ref([])
const attachemntErrors = ref([])
const formErrors = ref({})

const form = useForm({
    body: '',
    attachments: [],
    group_id: null,
    deleted_file_ids: [],
    _method: 'POST'
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const computedAttachments = computed(() => {

    return [...attachmentFiles.value, ...(props.post.attachments || [])]
})

const showExtensionsText = computed(() => {
    for (let myFile of attachmentFiles.value) {
        const file = myFile.file
        let parts = file.name.split('.')
        let ext = parts.pop().toLowerCase()
        if (!attachmentExtensions.includes(ext)) {
            return true;
        }
    }
    return false;
})

const emit = defineEmits(['update:modelValue', 'hide'])

watch(() => props.post, () => {
    form.body = props.post.body || ''

})

function resetModal() {
    form.reset()
    formErrors.value = {}
    attachmentFiles.value = []
    showExtensionsText.value = false
    attachemntErrors.value = []
    if (props.post.attachments) {
        props.post.attachments.forEach(file => file.deleted = false)
    }
}

function closeModal() {
    show.value = false
    emit('hide')
    resetModal()
}


function submit() {
    if (props.group) {
        form.group_id = props.group.id
    }
    form.attachments = attachmentFiles.value.map(myFile => myFile.file)
    if (props.post.id) {
        form._method = 'PUT'
        form.post(route('post.update', props.post.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
            },
            onError: (errors) => {
                processErrors(errors)
            }
        })
    } else {
        form.post(route('posts.create'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
            },
            onError: (errors) => {
                processErrors(errors)
            }
        })
    }
}

function processErrors(errors) {
    formErrors.value = errors
    for (const key in errors) {
        if (key.includes('.')) {
            const [, index] = key.split('.')
            attachemntErrors.value[index] = errors[key]
        }
    }
}

async function onAttachmentChoose($event) {
    for (const file of $event.target.files) {

        const myFile = {
            file,
            url: await readFile(file)
        }
        attachmentFiles.value.push(myFile)
    }
    $event.target.value = null
    console.log(attachmentFiles.value)
}

async function readFile(file) {
    return new Promise((res, rej) => {
        if (isImage(file)) {
            const reader = new FileReader()
            reader.onload = () => {
                res(reader.result)
            }
            reader.onerror = rej
            reader.readAsDataURL(file)
        } else {
            res(null)
        }
    })

}

function removeFile(myFile) {
    if (myFile.file) {
        attachmentFiles.value = attachmentFiles.value.filter(f => f !== myFile)
    } else {
        form.deleted_file_ids.push(myFile.id)
        myFile.deleted = true
    }
}

function undoDelete(myFile) {
    myFile.deleted = false
    form.deleted_file_ids = form.deleted_file_ids.filter(id => myFile.id !== id)
}

// function getAiContent() {
//     if (!form.body) {
//         return;
//     }
//     instance.post(route('post.aiContent'), {
//         prompt: form.body
//     })
//         .then(({data}) => {
//             form.body = data.content
//         })
//         .catch(err => {
//             console.log(err)
//         })
// }
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

                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded bg-white
                                 text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="flex items-center justify-between py-3 px-4  font-medium bg-gray-100 text-gray-900"
                                >
                                    {{ post.id ? 'Update Post' : 'Create Post' }}
                                    <button class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center
                                            justify-center" @click="closeModal">
                                        <XMarkIcon class="w-4 h-4"/>
                                    </button>
                                </DialogTitle>
                                <div class="p-4  ">
                                    <PostUserHeader :post="post" :showTime="false" class="mb-4"/>


                                    <div v-if="formErrors.group_id" class="bg-red-500 py-2 px-3
                                        rounded text-white mb-3">
                                        {{ formErrors.group_id }}
                                    </div>

                                    <ckeditor v-model="form.body" :config="editorConfig" :editor="editor"></ckeditor>

                                    <div v-if="showExtensionsText" class="border-l-4 border-amber-500 py-2 px-3
                                            bg-amber-100 mt-3 text-gray-800">
                                        Files must be one of the following extensions <br>
                                        <small>{{ attachmentExtensions.join(', ') }}</small>
                                    </div>

                                    <div v-if="formErrors.attachments"
                                         class="border-l-4 border-amber-500 py-2 px-3
                                            bg-amber-100 mt-3 text-gray-800">
                                        {{ formErrors }}
                                    </div>

                                    <!--                                    <button @click="getAiContent">Ai Post</button>-->

                                    <div :class="[
                                            computedAttachments.length === 1 ? 'grid-cols-1 ' : 'grid-cols-2'
                                        ]" class="grid  gap-3 my-3">
                                        <div v-for="(myFile, ind) of computedAttachments ">
                                            <div :class="attachemntErrors[ind] ? 'border-red-500' : ''" class="group aspect-square  bg-blue-100 flex flex-col items-center
                                                justify-center text-gray-500 relative
                                                border-2">
                                                <div v-if="myFile.deleted" class="absolute z-10 left-0 bottom-0 right-0 py-2
                                                    px-3 text-sm bg-black text-white flex justify-between items-center ">
                                                    To be deleted
                                                    <ArrowUturnLeftIcon class="w-4 h-4 cursor-pointer"
                                                                        @click="undoDelete(myFile)"/>
                                                </div>
                                                <button class="absolute z-20 right-3 top-3 w-7 h-7 flex items-center
                                                    justify-center bg-black/30 text-white rounded-full
                                                    hover:bg-black/40"
                                                        @click="removeFile(  myFile)">
                                                    <XMarkIcon class="h-5 w-5 "/>
                                                </button>

                                                <img v-if="isImage(myFile.file ||myFile)"
                                                     :class="myFile.deleted ? 'opacity-50' : ''"
                                                     :src="myFile.url"
                                                     class="object-contain ">
                                                <div v-else
                                                     :class="myFile.deleted ? 'opacity-50' : ''"
                                                     class=" flex flex-col justify-center items-center px-3">
                                                    <PaperClipIcon class="w-10 h-10 mb-3 "/>

                                                    <small class="text-center">
                                                        {{ (myFile.file || myFile).name }}
                                                    </small>
                                                </div>

                                            </div>
                                            <small class="text-red-500">{{ attachemntErrors[ind] }}</small>
                                        </div>
                                    </div>

                                </div>

                                <div class=" flex gap-2  py-3 px-4 ">
                                    <button
                                        class="  flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white
                                             shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2
                                             focus-visible:outline-offset-2 focus-visible:outline-indigo-600 relative w-full"
                                        type="button"
                                        @click=""
                                    >
                                        <PaperClipIcon class="w-4 h-4 mr-2"/>
                                        Attach file
                                        <input class="absolute left-0 top-0
                                                right-0 bottom-0 opacity-0" multiple type="file"
                                               @change="onAttachmentChoose" @click.stop>
                                    </button>
                                    <button
                                        class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white
                                             shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2
                                             focus-visible:outline-offset-2 focus-visible:outline-indigo-600 relative w-full"
                                        type="button"
                                        @click="submit"
                                    >
                                        <BookmarkIcon class="w-4 h-4 mr-2"/>
                                        Submit
                                    </button>

                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>
