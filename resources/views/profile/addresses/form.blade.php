<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <h2 class="font-semibold mb-3">
                <i class="fa-solid fa-user"></i>
                Kişisel Bilgiler
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Adres Başlığı</span>
                    </label>
                    <select name="title" class="select select-bordered @error('title') select-error @enderror">
                        <option value="">Seçiniz</option>
                        @foreach (['home' => 'Ev Adresi', 'work' => 'İş Adresi', 'summer_house' => 'Yazlık', 'family' => 'Aile Evi', 'other' => 'Diğer'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('title', $address?->title) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('title')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Telefon</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone', $address?->phone) }}" required
                        class="input input-bordered @error('phone') input-error @enderror" placeholder="5XX XXX XX XX">
                    @error('phone')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Ad</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name', $address?->first_name) }}" required
                        class="input input-bordered @error('first_name') input-error @enderror">
                    @error('first_name')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Soyad</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name', $address?->last_name) }}" required
                        class="input input-bordered @error('last_name') input-error @enderror">
                    @error('last_name')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>

            <div class="divider"></div>
            <h2 class="font-semibold mb-3">
                <i class="fa-solid fa-map-pin"></i>
                Adres Bilgileri
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Adres</span>
                    </label>
                    <textarea name="address" rows="3" required
                        class="textarea textarea-bordered @error('address') textarea-error @enderror">{{ old('address', $address?->address) }}</textarea>
                    @error('address')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Ülke</span>
                    </label>
                    <select name="country" class="select select-bordered @error('country') select-error @enderror" required>
                        @foreach (['Turkey' => 'Türkiye', 'Cyprus' => 'Kıbrıs'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('country', $address?->country) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Şehir</span>
                    </label>
                    <input type="text" name="city" value="{{ old('city', $address?->city) }}" required
                        class="input input-bordered @error('city') input-error @enderror">
                    @error('city')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">İlçe</span>
                    </label>
                    <input type="text" name="state" value="{{ old('state', $address?->state) }}" required
                        class="input input-bordered @error('state') input-error @enderror">
                    @error('state')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Posta Kodu</span>
                    </label>
                    <input type="text" name="zip_code" value="{{ old('zip_code', $address?->zip_code) }}" required
                        class="input input-bordered @error('zip_code') input-error @enderror" maxlength="5">
                    @error('zip_code')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Firma Adı</span>
                    </label>
                    <input type="text" name="company_name" value="{{ old('company_name', $address?->company_name) }}"
                        class="input input-bordered @error('company_name') input-error @enderror">
                    @error('company_name')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Vergi No</span>
                    </label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $address?->tax_number) }}"
                        class="input input-bordered @error('tax_number') input-error @enderror">
                    @error('tax_number')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Vergi Dairesi</span>
                    </label>
                    <input type="text" name="tax_office" value="{{ old('tax_office', $address?->tax_office) }}"
                        class="input input-bordered @error('tax_office') input-error @enderror">
                    @error('tax_office')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>

            <div class="form-control mt-4">
                <label class="cursor-pointer label justify-start gap-3">
                    <input type="checkbox" name="is_default" value="1" class="checkbox checkbox-primary"
                        @checked(old('is_default', $address?->is_default))>
                    <span class="label-text">Bu adresi varsayılan adresim olarak ayarla</span>
                </label>
            </div>

            <div class="card-actions justify-end mt-6">
                <a href="{{ route('profile.addresses.index') }}" class="btn btn-ghost">Vazgeç</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
