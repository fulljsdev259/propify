<template>
    <ui-card shadow="always">
        <div class="left-circle icon-tools">
        </div>
        <div class="right-text">
            <p class="title">{{$t('resident.emergency.title')}}</p>
            <p class="desc">{{$t('resident.emergency.desc')}}</p>
            <p class="phone-number">079 999 99 99</p>
        </div>
        
    </ui-card>
</template>

<script>

    export default {
        name: 'p-emergency-card',
        data () {
            return {
                
            }
        },
        async mounted () {
            
        },
    }
</script>

<style lang="sass" scoped>
    .ui-card
        padding: 10px


        /deep/ .ui-card__body
            display: flex
            align-items: center
            
            .left-circle
                width: 50px
                height: 50px
                font-size: 30px
                color: var(--primary-color)
                display: -webkit-box
                display: -ms-flexbox
                display: flex
                -webkit-box-align: center
                -ms-flex-align: center
                align-items: center
                -webkit-box-pack: center
                -ms-flex-pack: center
                justify-content: center
                border-radius: 50%
                border: 8px var(--primary-color) solid
                -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24)
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24)
                
            .right-text
                flex: 1
                padding-left: 20px

                .desc
                    margin-bottom: 5px

                .phone-number
                    font-weight: bolder
                    font-size: 15px
                    margin-top: 5px

    @media only screen and (max-width: 676px)
        .ui-card
            /deep/ .ui-card__body
                display: flex
                flex-direction: column

                .right-text
                    display: flex
                    flex-direction: column
                    align-items: center
                    padding-left: 0

                    .title
                        margin-bottom: 0
    
</style>