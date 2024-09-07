<script setup>

import UserListItem from "@/Components/app/UserListItem.vue";

defineProps({
    users: Array
})

</script>

<template>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400   text-center p-3">
            You dont have friends yet.
        </div>
        <div v-else>
            <UserListItem v-for="user of users"
                          :key="user.id"
                          :user="user"
                          class="rounded-lg"/>
        </div>
    </div>
</template>

<style scoped>

</style>
