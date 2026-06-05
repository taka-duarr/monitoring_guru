<div x-data="{ open: false, data: {}, editUrl: '' }"
     x-show="open"
     @open-detail-modal.window="open = true; data = $event.detail.guru; editUrl = $event.detail.editUrl"
     class="modal-backdrop"
     style="display: none;"
     x-transition:opacity.duration.300ms>
     
     <!-- Modal Dialog -->
     <div class="modal-container modal-container-md"
          @click.away="open = false"
          x-show="open"
          x-transition:scale.origin.center.duration.300ms>
          
          <!-- Header Profile Summary -->
          <div class="modal-header" style="padding: 20px 24px;">
              <div class="d-flex align-center gap-4">
                  <!-- Photo Preview with border -->
                  <template x-if="data.foto">
                      <img :src="data.foto" alt="" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary-100);">
                  </template>
                  <template x-if="!data.foto">
                      <div class="d-flex align-center justify-center text-white font-bold" 
                           style="width: 72px; height: 72px; border-radius: 50%; font-size: 24px; border: 3px solid var(--color-primary-100); background-color: var(--color-primary-800);">
                           <span x-text="data.name ? data.name.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase() : ''"></span>
                      </div>
                  </template>
                  
                  <div>
                      <h3 style="font-size: 18px; font-weight: 600; color: var(--color-neutral-900); margin: 0 0 4px 0;" x-text="data.name">Detail Guru</h3>
                      <div class="d-flex align-center gap-2">
                          <span class="badge" 
                                :class="{
                                    'badge-success': data.status === 'Aktif',
                                    'badge-warning': data.status === 'Cuti',
                                    'badge-neutral': data.status !== 'Aktif' && data.status !== 'Cuti'
                                }"
                                x-text="data.status"></span>

                      </div>
                  </div>
              </div>
              
              <!-- Close button -->
              <button type="button" class="modal-close-btn" @click="open = false" aria-label="Tutup Modal">
                  <i class="ti ti-x"></i>
              </button>
          </div>
          
          <!-- Detailed Columns -->
          <div class="modal-body">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  
                  <!-- Left Column -->
                  <div class="d-flex flex-column gap-4">
                      
                      <div>
                          <span style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--color-neutral-400); letter-spacing: 0.05em; margin-bottom: 2px;">Jenis Kelamin</span>
                          <span style="font-size: 14px; font-weight: 500; color: var(--color-neutral-800);" x-text="data.jenis_kelamin"></span>
                      </div>
                      
                      <div>
                          <span style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--color-neutral-400); letter-spacing: 0.05em; margin-bottom: 2px;">No. Telepon</span>
                          <span style="font-size: 14px; font-weight: 500; color: var(--color-neutral-800);" x-text="data.no_telp"></span>
                      </div>
                  </div>
                  
                  <!-- Right Column -->
                  <div class="d-flex flex-column gap-4">

                      

                  </div>
                  
              </div>
          </div>
          
          <!-- Footer Buttons -->
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="open = false">Tutup</button>
              <a :href="editUrl" class="btn btn-primary d-flex align-center gap-2" style="text-decoration: none;">
                  <i class="ti ti-pencil"></i> Edit Data
              </a>
          </div>
     </div>
</div>
