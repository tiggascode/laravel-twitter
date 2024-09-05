<script setup>

import {EllipsisVerticalIcon, EyeIcon, PencilIcon, TrashIcon} from "@heroicons/vue/20/solid/index.js";
import {ClipboardIcon} from "@heroicons/vue/24/outline/index.js";
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

defineEmits(['edit', 'delete', 'pin'])

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
const pinAllowed = computed(() => {
    return user.value.id === authUser.id || props.post.group && props.post.group.role === 'admin'
})

const isPinned = computed(() => {
    if (group?.id) {
        return group?.pinned_post_id === props.post.id
    } else {
        return authUser?.pinned_post_id === props.post.id
    }
})

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
                            @click="$emit('pin')"
                        >

                            <svg id="Layer_1" class="mr-2 h-5 w-5 "
                                 enable-background="new 0 0 122.879 122.867" version="1.1" viewBox="0 0 122.879 122.867"
                                 x="0px"
                                 xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                 y="0px"><g>
                                <path clip-rule="evenodd"
                                      d="M83.88,0.451L122.427,39c0.603,0.601,0.603,1.585,0,2.188l-13.128,13.125 c-0.602,0.604-1.586,0.604-2.187,0l-3.732-3.73l-17.303,17.3c3.882,14.621,0.095,30.857-11.37,42.32 c-0.266,0.268-0.535,0.529-0.808,0.787c-1.004,0.955-0.843,0.949-1.813-0.021L47.597,86.48L0,122.867l36.399-47.584L11.874,50.76 c-0.978-0.98-0.896-0.826,0.066-1.837c0.24-0.251,0.485-0.503,0.734-0.753C24.137,36.707,40.376,32.917,54.996,36.8l17.301-17.3 l-3.733-3.732c-0.601-0.601-0.601-1.585,0-2.188L81.691,0.451C82.295-0.15,83.279-0.15,83.88,0.451L83.88,0.451z"
                                      fill-rule="evenodd"/></g></svg>
                            {{ isPinned ? 'Unpin' : 'Pin' }}
                        </button>
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
