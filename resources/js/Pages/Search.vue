<script setup>

import UserListItem from "@/Components/app/UserListItem.vue";
import GroupItem from "@/Components/app/GroupItem.vue";
import PostList from "@/Components/app/PostList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    search: String,
    users: Array,
    groups: Array,
    posts: Object
})


</script>

<template>
    <AuthenticatedLayout>

        <div class="p-3 overflow-auto  h-full">
            <div v-if="!search.startsWith('#')" class="grid grid-cols-1 gap-3 sm:grid-cols-2 ">
                <div class="shadow bg-white dark:bg-slate-900 dark:text-gray-100 p-3 rounded mb-3">
                    <h2 class="text-lg font-bold">Users</h2>
                    <div class="grid-cols-2">
                        <UserListItem v-for="user of users" v-if="users.length" :user="user"/>
                        <div v-else class="text-gray-500 py-8 text-center">
                            No users were found
                        </div>
                    </div>
                </div>
                <div class="shadow bg-white dark:bg-slate-900 dark:text-gray-100 p-3 rounded mb-3">
                    <h2 class="text-lg font-bold">Groups</h2>
                    <div class="grid-cols-2">
                        <GroupItem v-for="group of groups" v-if="groups.length" :group="group"/>
                        <div v-else class="text-gray-500 py-8 text-center">
                            No groups were found
                        </div>
                    </div>
                </div>
            </div>

            <div class="shadow bg-white dark:bg-slate-900 dark:text-gray-100 p-3 rounded mb-3 ">
                <h2 class="text-lg font-bold">Posts</h2>
                <PostList v-if="posts.data.length" :posts="posts.data"/>
                <div v-else class="text-gray-500 py-8 text-center">
                    No posts were found
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<style scoped>

</style>
