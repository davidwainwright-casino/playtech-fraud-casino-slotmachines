import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-{{ component }}', IndexField)
  app.component('detail-{{ component }}', DetailField)
  app.component('form-{{ component }}', FormField)
})
