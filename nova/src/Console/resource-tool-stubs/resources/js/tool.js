import Tool from './components/Tool'

Nova.booting((app, store) => {
  app.component('{{ component }}', Tool)
})
