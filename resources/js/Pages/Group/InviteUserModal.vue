<script setup>
import {computed, ref} from 'vue'
import {BookmarkIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import {useForm, usePage} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import BaseModal from "@/Components/app/BaseModal.vue";


const props = defineProps({
    modelValue: Boolean,
})

const page = usePage()

const formErrors = ref({})

const form = useForm({
    email: '',
})


const emit = defineEmits(['update:modelValue', 'hide', 'create'])

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

function closeModal() {
    show.value = false
    emit('hide')
    resetModal()
}

function resetModal() {
    form.reset()
    formErrors.value = {}

}

function submit() {
    form.post(route('group.inviteUsers', page.props.group.slug), {
        onsuccess(res) {
            console.log(res)
            closeModal()
        },
        onError(err) {
            console.log(err)
        }
    })
}


</script>


<template>
    <BaseModal v-model="show" title="Invite Users" @hide="closeModal">
        <div class="p-4 dark:text-gray-100">
            <div class="mb-3">
                <label>Username or email</label>
                <TextInput
                    v-model="form.email"
                    :class="page.props.errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    autofocus
                    class="mt-1 block w-full"
                    required
                    type="text"

                />
            </div>
            <div class="text-red-500">{{ page.props.errors.email }}</div>
        </div>

        <div class=" flex gap-2 justify-end py-3 px-4 ">
            <button class="text-gray-800 bg-gray-100 hover:bg-gray-200 flex
                                        gap-1 items-center justify-center rounded-md py-2 px-4  "
                    @click="show = false">
                <XMarkIcon class="w-5 h-5"/>
                Cancel
            </button>
            <button
                class="flex items-center justify-center rounded-md bg-indigo-600
                                            px-3 py-2 text-sm font-semibold text-white shadow-sm
                                            hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2
                                            focus-visible:outline-offset-2 focus-visible:outline-indigo-600
                                            relative "
                type="button"
                @click="submit"
            >
                <BookmarkIcon class="w-4 h-4 mr-2"/>
                Submit
            </button>

        </div>
    </BaseModal>

</template>
