export default {
  state: () => ({
    notifications: [],
    notificationsShown: false,
    unreadNotifications: false,
  }),

  getters: {
    notifications: s => s.notifications,
    notificationsShown: s => s.notificationsShown,
    unreadNotifications: s => s.unreadNotifications,
  },

  mutations: {
    toggleNotifications(state) {
      state.notificationsShown = !state.notificationsShown
      localStorage.setItem('nova.mainMenu.open', state.notificationsShown)
    },
  },

  actions: {
    async fetchNotifications({ state }) {
      const {
        data: { notifications, unread },
      } = await Nova.request().get(`/nova-api/nova-notifications`)

      state.notifications = notifications
      state.unreadNotifications = unread
    },

    async markNotificationAsRead({ state, dispatch }, id) {
      await Nova.request().post(`/nova-api/nova-notifications/${id}/read`)
      dispatch('fetchNotifications')
    },

    async deleteNotification({ state, dispatch }, id) {
      await Nova.request().delete(`/nova-api/nova-notifications/${id}/delete`)
      dispatch('fetchNotifications')
    },

    async markAllNotificationsAsRead({ state, dispatch }, id) {
      await Nova.request().post(`/nova-api/nova-notifications/read-all`)
      dispatch('fetchNotifications')
    },
  },
}
