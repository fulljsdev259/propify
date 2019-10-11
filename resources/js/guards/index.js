import hasPermission from 'guards/hasPermissionGuard'
import isAdmin from 'guards/isAdminGuard'
import isAuthenticated from 'guards/isAuthenticatedGuard'
import isGuest from 'guards/isGuestGuard'
import isResident from 'guards/isResidentGuard'
import isService from 'guards/isServiceGuard'
import isManager from 'guards/isManagerGuard'


export const hasPermissionGuard = hasPermission
export const isAdminGuard = isAdmin
export const isAuthenticatedGuard = isAuthenticated
export const isGuestCard = isGuest
export const isResidentGuard = isResident
export const isServiceGuard = isService
export const isManagerGuard = isManager