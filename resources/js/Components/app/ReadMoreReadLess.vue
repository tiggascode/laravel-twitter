<script setup>

import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";

defineProps({
    content: String,
    contentClass: String,
})


</script>

<template>
    <Disclosure v-slot="{ open }">
        <div v-if="!open " class="ck-content-output" v-html="content.substring(0,200)"></div>

        <template v-if="content && content.length > 200">
            <DisclosurePanel>

                <div :class="contentClass" class="ck-content-output" v-html="content"></div>

            </DisclosurePanel>
            <div class="flex justify-end">
                <DisclosureButton class="text-blue-500 hover:underline">
                    {{ open ? 'Read less' : 'Read more' }}
                </DisclosureButton>
            </div>
        </template>
    </Disclosure>
</template>

<style scoped>

</style>
