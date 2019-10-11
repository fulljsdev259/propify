<template>
    <div class="listing-details" v-if="!_.isEmpty(listing)">
        <el-row :gutter="10">
            <el-col :md="6" :sm="12">
                <p class="listing-label">{{$t('general.name')}}</p>
                <p v-if="listing.resident">
                    <router-link :to="{name: 'adminResidentsEdit', params: {id: listing.resident.id}}">
                        {{listing.resident.name}}
                    </router-link>
                </p>
                <p v-else>
                    <router-link :to="{name: 'adminUsersEdit', params: {id: listing.user_id}}">
                        {{listing.user ? listing.user.name:""}}
                    </router-link>
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="listing-label">{{$t('models.listing.type.label')}}</p>
                <p>
                    {{$t(`models.listing.type.${listingConstants.type[listing.type]}`)}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="listing-label">{{$t('models.listing.visibility.label')}}</p>
                <p>
                    {{$t(`models.listing.visibility.${listingConstants.visibility[listing.visibility]}`)}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="listing-label">{{$t('models.listing.status.label')}}</p>
                <p>
                    {{$t(`models.listing.status.${listingConstants.status[listing.status]}`)}}
                </p>
            </el-col>
            <el-col :md="2" :sm="12">
                <p class="listing-label">{{$t('models.listing.likes')}}</p>
                <p>
                    {{listing.likes_counter}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="listing-label">{{$t('models.listing.published_at')}}</p>
                <p>
                    {{listing.published_at}}
                </p>
            </el-col>
        </el-row>
        <el-row v-if="listing.title">
            <el-col :span="24">
                <p class="listing-label">{{$t('models.listing.listing_title')}}</p>
                <p>
                    {{listing.title}}
                </p>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="24">
                <p class="listing-label">{{$t('general.content')}}</p>
                <p>
                    {{listing.content}}
                </p>
            </el-col>
        </el-row>
        <gallery :data="listing.media" :withDelete="false" v-if="listing.media && listing.media.length"></gallery>
    </div>
</template>

<script>
    import Gallery from 'components/RequestMedia';

    export default {
        name: "ListingDetails",
        components: {
            Gallery
        },
        props: {
            listing: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                listingConstants: this.$constants.listings
            }
        }
    }
</script>

<style scoped>
    .listing-label {
        color: #409EFF;
    }
</style>
