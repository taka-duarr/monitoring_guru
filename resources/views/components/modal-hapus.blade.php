<div x-data="{ open: false, url: '', name: '', loading: false }"
     x-show="open"
     @confirm-delete.window="open = true; url = $event.detail.url; name = $event.detail.name; loading = false"
     class="modal-backdrop"
     style="display: none;"
     x-transition:opacity.duration.300ms>
     
     <!-- Modal Card -->
     <div class="modal-container modal-container-sm"
          @click.away="if(!loading) open = false"
          x-show="open"
          x-transition:scale.origin.center.duration.300ms>
          
          <div class="modal-body text-center" style="padding: 28px;">
              <!-- Danger Icon -->
              <div class="d-flex justify-center mb-4">
                  <div class="d-flex align-center justify-center bg-danger-50 text-danger-500" 
                       style="width: 64px; height: 64px; border-radius: 50%; font-size: 32px; background-color: var(--color-danger-50);">
                      <i class="ti ti-trash-x"></i>
                  </div>
              </div>
              
              <!-- Content -->
              <h3 style="font-size: 18px; font-weight: 600; color: var(--color-neutral-900); margin: 0 0 8px 0;">Hapus Data Guru?</h3>
              <p style="font-size: 14px; color: var(--color-neutral-500); margin: 0 0 24px 0; line-height: 1.5;">
                  Aksi ini tidak dapat dibatalkan. Data <strong class="text-neutral-900 font-semibold" x-text="name"></strong> akan dihapus permanen dari sistem.
              </p>
              
              <!-- Actions Row -->
              <div class="d-flex gap-3 justify-center">
                  <button type="button" 
                          class="btn btn-secondary" 
                          style="flex: 1; justify-content: center;"
                          :disabled="loading" 
                          @click="open = false">
                      Batal
                  </button>
                  
                  <form :action="url" method="POST" class="m-0" style="flex: 1;" @submit="loading = true">
                      @csrf
                      @method('DELETE')
                      <button type="submit" 
                              class="btn btn-danger" 
                              style="width: 100%; justify-content: center; display: inline-flex; align-items: center; gap: 8px;"
                              :disabled="loading">
                          <template x-if="loading">
                              <span class="table-spinner" style="width: 14px; height: 14px; border-width: 2px; border-color: white; border-top-color: transparent;"></span>
                          </template>
                          <span x-text="loading ? 'Menghapus...' : 'Ya, Hapus'"></span>
                      </button>
                  </form>
              </div>
          </div>
     </div>
</div>
