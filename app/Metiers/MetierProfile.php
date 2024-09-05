<?php

namespace App\Metiers;

use App\Models\Profile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MetierProfile
{
    private $profile;

    /**
     * @param int|null $idProfile
     *
     * @throws ModelNotFoundException
     */
    public function __construct(int $idProfile = null)
    {
        if ($idProfile !== null) {
            $this->profile = Profile::findOrFail($idProfile);
        }
    }

    /**
     * Get the current profile.
     *
     * @return Profile|null
     */
    public function getCurrent(): ?Profile
    {
        return $this->profile;
    }

    /**
     * Get all profiles by status.
     *
     * @param int $statut
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProfiles(int $statut, bool $withStatus = false)
    {
        $query = Profile::where('statut', $statut)->paginate(10);

        if ($withStatus) $query->makeVisible(['statut']);

        return $query;
    }

    /**
     * Create a new profile instance.
     *
     * @param string|null $nom
     * @param string|null $prenom
     * @param int|null $statut
     * @param array|UploadedFile|UploadedFile[]|null $image
     *
     * @return void
     *
     * @throws Throwable
     */
    public function set(string $nom = null, string $prenom = null, int $statut = null, array|UploadedFile $image = null) : void
    {
        if ($this->profile === null) {
            $this->profile = new Profile();
        }
        if ($nom !== null) {
            $this->profile->nom = $nom;
        }

        if ($prenom !== null) {
            $this->profile->prenom = $prenom;
        }

        if ($image !== null) {
            $this->profile->image = $this->insertOrUpdateImage($image);
        }

        if ($statut !== null) {
            $this->profile->statut = $statut;
        }


        $this->profile->saveOrFail();
    }

    /**
     * Save the profile to the database.
     *
     * @param array $data
     * @return Profile
     */
    public function saveProfile(array $data): Profile
    {
        $this->profile->fill($data);
        $this->profile->save();
        return $this->profile;
    }

    /**
     * Delete the profile.
     *
     * @return bool|null
     */
    public function deleteProfile(): ?bool
    {
        if ($this->profile->image !== null) {
            $this->deleteCurrentImage();
        }
        return $this->profile->delete();
    }

    private function insertOrUpdateImage($image)
    {
        if ($this->profile->image !== null) {
            $this->deleteCurrentImage();
        }

        return $image->store('images');
    }

    /**
     * @return void
     */
    private function deleteCurrentImage(): void
    {
        Storage::delete($this->profile->image);
    }
}
