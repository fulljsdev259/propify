import {mapActions} from 'vuex';
import { EventBus } from '../event-bus.js';

export default {
    data() {
        return {
            avatar: ''
        }
    },
    methods: {
        ...mapActions(['uploadUserAvatar']),
        async uploadAvatar(userId, merge_in_audit) {
            await this.uploadUserAvatar({
                id: userId,
                image_upload: this.avatar,
                merge_in_audit: merge_in_audit
            });

            if( this.$store.getters.loggedInUser.id == userId ) {
                EventBus.$emit('avatar-update');
            }
        },
        cropped(e) {
            this.avatar = e;
        },
        async uploadAvatarIfNeeded(userId, merge_in_audit) {
            if (this.avatar.length) {
                return await this.uploadAvatar(userId, merge_in_audit);
            }
        }
    }
};
