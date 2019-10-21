<template>
    <div class="progress-bar-container">
         <el-row :gutter="24" type="flex">
            <el-col :span="12">
                <div class="progress-label">
                    <span>{{ label }} ({{ value }})</span>
                </div>
            </el-col>
            <el-col :span="12">
                <div class="progress-bar">
                    <el-progress :text-inside="true" :stroke-width="20" :color="color" :percentage="parseFloat((value*100/maxValue).toFixed(2))"></el-progress>
                </div>
            </el-col>
        </el-row>
               
    </div>
</template>
<script>
export default {
    props: {
        value: {
            type: Number,
        },
        maxValue: {
            type: Number
        },
        color: {
            type: String
        },
        label: {
            type: String
        }
    },
    data() {
        return {
        }
    },
    computed: {
      style () {
        return 'color: ' + this.color;
      }
    },

}
</script>
<style lang="scss" scoped>
    .progress-bar-container {

        .el-row {
            margin: 0 !important;
            padding: 20 !important;

            .progress-label {
                display: flex;
                justify-content: center;
                font-size: 14px;
                font-weight: 400;
                font-family: roboto;
                color: darken(#fff, 50%);
                float: left;
                padding-right: 10px;
            }
            .progress-bar {
                width: 100%;
                .progress-status {
                    display: flex;
                    justify-content: space-between;

                    .progress-value {
                        font-size: 14px;
                    }
                    .progress-percent {
                        font-weight: bold;
                        font-size: 14px;
                    }
                }
            }
        }
    }
</style>
<style lang="scss">
    .progress-bar .el-progress-bar__innerText {
        text-shadow: 0 0 3px #000;
    }
</style>