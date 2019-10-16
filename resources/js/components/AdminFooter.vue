<template>
    <footer ref="footer" class="footer">
        <div class="footer-content">
            <div class="footer-content-left">
                <p class="footer-content-left-text" v-html="$t('general.footer_text.left_side_text')"></p>
            </div>
            <div class="footer-content-right">
                <a href="https://www.propify.ch " target="_blank">
                    <img class="footer-content-right-logo" src="~img/propify-logo.svg"/>
                </a>
                <div class="footer-content-right-text">
                    <span>&copy; {{currentYear}} {{$t('general.footer_text.company_name')}} <br>
                        {{$t('general.footer_text.all_rights_saved')}}</span>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
    export default {
        name: "AdminFooter",
        data(){
            return {
                currentYear: null
            }
        },

        mounted() {
            let d = new Date();
            this.currentYear = d.getFullYear();
        }
    }
</script>

<style lang="scss" scoped>
    .footer{
        margin: 2rem 20px 0;
        background: #fff;
        padding: 0px 0px 0px 15px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;

        &-content{
            display: flex;
            justify-content: space-between;
            align-items: center;

            &-left{
                width: 73%;

                &-text{
                    font-size: 12px;
                }
            }

            &-right{
                font-size: 12px;
                padding-right: 15px;
                width: 24%;
                display: flex;
                justify-content: flex-end;

                &-logo{
                   width: 35px;
                   height: 35px;
                   margin-right: 15px;
                }
            }
        }
    }
</style>
