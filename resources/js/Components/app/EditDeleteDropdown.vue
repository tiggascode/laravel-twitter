<script setup>

import {EllipsisVerticalIcon, EyeIcon, PencilIcon, TrashIcon} from "@heroicons/vue/20/solid/index.js";
import {ClipboardIcon} from "@heroicons/vue/24/outline/index.js";
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

defineEmits(['edit', 'delete'])

const props = defineProps({
    post: {
        type: Object,
        default: null
    },
    comment: {
        type: Object,
        default: null
    },
})

const authUser = usePage().props.auth.user
const group = usePage().props.group
const user = computed(() => props.post?.user || props.comment?.user)


const deleteAllowed = computed(() => {
    if (user.value.id === authUser.id) return true

    if (props.post.user.id === authUser.id) return true

    return !props.comment && props.post.group?.role === 'admin'

})

const editAllowed = computed(() => user.value.id === authUser.id)

function copyToClipboard() {
    const textToCopy = route('post.view', props.post.id)

    const tempInput = document.createElement('input')
    tempInput.value = textToCopy
    document.body.appendChild(tempInput)

    tempInput.select()
    document.execCommand('copy')

    document.body.removeChild(tempInput)
}
</script>

<template>
    <Menu as="div" class="relative inline-block text-left ">
        <div>
            <MenuButton
                class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center
                                justify-center">

                <EllipsisVerticalIcon
                    aria-hidden="true"
                    class="w-5 h-5"
                />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute z-20 right-0 mt-2 w-48 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <button :class="[
                                  active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                ]"
                                @click="copyToClipboard"

                        >
                            <ClipboardIcon
                                aria-hidden="true"
                                class="mr-2 h-5 w-5 "
                            />
                            Copy Post Url
                        </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <Link :class="[
                                active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                              ]"
                              :href="route('post.view',post.id)"

                        >
                            <EyeIcon
                                aria-hidden="true"
                                class="mr-2 h-5 w-5 "
                            />
                            Open Post
                        </Link>
                    </MenuItem>

                    <MenuItem v-if="editAllowed" v-slot="{ active }">
                        <button
                            :class="[
                  active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                            @click="$emit('edit')"
                        >
                            <PencilIcon
                                aria-hidden="true"
                                class="mr-2 h-5 w-5 "
                            />
                            Edit
                        </button>
                    </MenuItem>
                    <MenuItem
                        v-if="deleteAllowed"
                        v-slot="{ active }">
                        <button
                            :class="[
                  active ? ' bg-indigo-500 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                            @click="$emit('delete')"
                        >
                            <TrashIcon
                                aria-hidden="true"
                                class="mr-2 h-5 w-5 "
                            />
                            Delete
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<style scoped>

</style>
