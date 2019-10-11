import axios from '@/axios'

export default {
    async upload ({}, {id, type, ...params}) {
        await axios.post(`${type}/${id}/media`, params)
    },
    async delete ({}, {id, type, media_id, ...params}) {
        await axios.delete(`${type}/${id}/media/${media_id}`, params)
    }
    // async upload ({}, {id, type, ...payload}) {
    //     try {
    //         if (!['buildings', 'listings', 'pinboard', 'residents', 'requests'].includes(type)) {
    //             throw new Error('Invalid type')
    //         }
    
    //         const request = `${type}/${id}/media`
    //         const {data} = await axios.post(request, payload)

    //         return Promise.resolve(data)
    //     } catch (err) {
    //         return Promise.reject(err)
    //     }
    // },
    // async delete ({}, {id, media_id, type, ...payload}) {
    //     try {
    //         if (!['buildings', 'listings', 'pinboard', 'residents', 'requests'].includes(type)) {
    //             throw new Error('Invalid type')
    //         }
    
    //         const request = `${type}/${id}/media/${media_id}`
    //         const {data} = await axios.delete(request, payload)

    //         return Promise.resolve(data)
    //     } catch (err) {
    //         return Promise.reject(err)
    //     }
    // }
}