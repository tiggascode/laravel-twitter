<script setup>

import FollowingListItems from "@/Components/app/FollowingListItems.vue";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";

defineProps({
    users: Array
})
</script>

<template>
    <div class="px-3 bg-white rounded dark:bg-slate-950
dark:border-slate-900 dark:text-gray-100  border py-3 h-full overflow-hidden  ">

        <div class="block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">My Followings</h2>
                        <svg :class="open ? 'rotate-90 transform' : ''"
                             class="w-6 h-6 transition-all"
                             fill="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd"
                                  d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                                  fill-rule="evenodd"/>
                        </svg>
                    </div>
                </DisclosureButton>

                <DisclosurePanel>
                    <FollowingListItems :users="users"/>
                </DisclosurePanel>
            </Disclosure>
        </div>

        <div class="h-full  flex-col overflow-hidden hidden lg:flex">
            <h2 class="text-xl font-bold ">My Follwoings</h2>

            <FollowingListItems :users="users"/>
        </div>
    </div>
</template>

<style scoped>

</style>
