<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {ChatBubbleLeftRightIcon, HandThumbUpIcon} from '@heroicons/vue/20/solid'
import PostUserHeader from "@/Components/app/PostUserHeader.vue";
import {router, useForm, usePage} from '@inertiajs/vue3'
import instance from "@/axiosClient.js";
import ReadMoreReadLess from "@/Components/app/ReadMoreReadLess.vue";
import EditDeleteDropdown from "@/Components/app/EditDeleteDropdown.vue";
import PostAttachment from "@/Components/app/PostAttachment.vue";
import CommentList from "@/Components/app/CommentList.vue";
import {computed} from "vue";
import UrlPreview from "@/Components/app/UrlPreview.vue";

const props = defineProps({
    post: Object

})

const authUser = usePage().props.auth.user
const group = usePage().props.group

const emit = defineEmits(['editClick', 'attachmentClick'])

const postBody = computed(() => {
    let content = props.post.body.replace(
        /(?:(\s+)|<p>)((#\w+)(?![^>]*<\/a>))/g,
        (match, group1, group2) => {
            const encodedGroup = encodeURIComponent(group2)
            return `${group1 || ''}<a href="/search/${encodedGroup}" class="hashtag">${group2}</a>`
        }
    )
    return content
})

const isPinned = computed(() => {
    if (group?.id) {
        return group?.pinned_post_id === props.post.id
    }
    return authUser?.pinned_post_id === props.post.id
})

function openEditModal() {
    emit('editClick', props.post)
}

function deletePost() {
    if (window.confirm('Are you sure you want to delete this post?')) {
        router.delete(route('post.destroy', props.post), {
            preserveScroll: true,


        })
    }
}

function openAttachment(ind) {
    emit('attachmentClick', props.post, ind)
}

function sendReaction() {
    instance.post(route('post.reaction', props.post), {
        type: 'like'
    })
        .then(({data}) => {
            props.post.current_user_has_reaction = data.current_user_has_reaction
            props.post.num_of_reactions = data.num_of_reactions
        })
}

function pinUnpinPost() {
    const form = useForm({
        forGroup: group?.id
    })
    let isPinned = false
    if (group?.id) {
        isPinned = group?.pinned_post_id === props.post.id
    } else {
        isPinned = authUser?.pinned_post_id === props.post.id
    }
    form.post(route('post.pinUnpin', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (group?.id) {
                group.pinned_post_id = isPinned ? null : props.post.id
            } else {
                authUser.pinned_post_id = isPinned ? null : props.post.id
            }
        }
    })
}


</script>

<template>
    <div class="bg-white border rounded p-4 mb-3">
        <div class="flex items-center justify-between  mb-3">
            <PostUserHeader :post="post"/>
            <div class="flex items-center">
                <div v-if="isPinned" class="flex items-center text-xs">
                    <svg id="Layer_1" class="mr-2 h-5 w-5 "
                         enable-background="new 0 0 122.879 122.867" version="1.1" viewBox="0 0 122.879 122.867"
                         x="0px"
                         xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         y="0px"><g>
                                <path clip-rule="evenodd"
                                      d="M83.88,0.451L122.427,39c0.603,0.601,0.603,1.585,0,2.188l-13.128,13.125 c-0.602,0.604-1.586,0.604-2.187,0l-3.732-3.73l-17.303,17.3c3.882,14.621,0.095,30.857-11.37,42.32 c-0.266,0.268-0.535,0.529-0.808,0.787c-1.004,0.955-0.843,0.949-1.813-0.021L47.597,86.48L0,122.867l36.399-47.584L11.874,50.76 c-0.978-0.98-0.896-0.826,0.066-1.837c0.24-0.251,0.485-0.503,0.734-0.753C24.137,36.707,40.376,32.917,54.996,36.8l17.301-17.3 l-3.733-3.732c-0.601-0.601-0.601-1.585,0-2.188L81.691,0.451C82.295-0.15,83.279-0.15,83.88,0.451L83.88,0.451z"
                                      fill-rule="evenodd"/></g></svg>
                    pinned
                </div>
                <EditDeleteDropdown :post="post" :user="post.user"
                                    @delete="deletePost"
                                    @edit="openEditModal"
                                    @pin="pinUnpinPost"/>
            </div>

        </div>
        <div class="mb-3">
            <ReadMoreReadLess :content="postBody"/>
            <UrlPreview :preview="post.preview" :url="post.preview_url"/>

        </div>
        <div :class="[
            post.attachments.length === 1 ? 'grid-cols-1 ' : 'grid-cols-2'
        ]" class="grid   gap-3 mb-3">
            <PostAttachment :attachments="post.attachments" @attachmentClick="openAttachment"/>
        </div>
        <Disclosure v-slot="{ open }">
            <!--            like and comment buttons-->
            <div class="flex gap-2 ">
                <button
                    :class="[
                    post.current_user_has_reaction ?
                    'bg-sky-100 hover:bg-sky-200' :
                    ' bg-gray-100 hover:bg-gray-200',

                ]"
                    class="text-gray-800 flex gap-1 items-center justify-center rounded-lg
                py-2 px-4 flex-1"
                    @click="sendReaction">
                    <HandThumbUpIcon class="w-5 h-5  "/>
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.current_user_has_reaction ? 'Unlike' : 'Like' }}

                </button>
                <DisclosureButton
                    class="text-gray-800 bg-gray-100 hover:bg-gray-200 flex gap-1 items-center justify-center rounded-lg
                py-2 px-4 flex-1">
                    <ChatBubbleLeftRightIcon class="w-5 h-5"/>
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Comment

                </DisclosureButton>
            </div>
            <DisclosurePanel class="comment-list mt-3 max-h-[400px] overflow-auto">
                <CommentList :data="{comments: post.comments}" :parent-comment="post.comments" :post="post"/>
            </DisclosurePanel>
        </Disclosure>

    </div>

</template>


