<?php

namespace App\Http\Controllers;

use App\Enums\Statut;
use App\Http\Requests\ProfileCreateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Metiers\MetierProfile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Récupère tous les profils actifs.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfiles(Request $request): JsonResponse
    {
        $metierProfile = new MetierProfile();
        $profiles = $metierProfile->getProfiles(Statut::Actif->value, !!$request->auth);

        return response()->json($profiles);
    }

    /**
     * Crée un nouveau profil.
     *
     * @param ProfileCreateRequest $request Les données validées pour la création du profil.
     *
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function setProfile(ProfileCreateRequest $request): JsonResponse
    {
        try {
            $metierProfile = new MetierProfile();
            $metierProfile->set(
                $request->input('nom'),
                $request->input('prenom'),
                $request->input('statut'),
                $request->file('image')
            );

            return response()->json($metierProfile->getCurrent());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création du profil.',
                'details' => $e->getMessage() // TODO remove in production
            ], 500);
        }
    }

    /**
     * Crée un nouveau profil.
     *
     * @param ProfileUpdateRequest $request Les données validées pour la création du profil.
     * @param int $id ID du profile à modifier
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function updateProfile(ProfileUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $metierProfile = new MetierProfile($id);
            $metierProfile->set(
                $request->input('nom'),
                $request->input('prenom'),
                $request->input('statut'),
                $request->file('image')
            );

            return response()->json($metierProfile->getCurrent());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la mise à jour du profil.',
                'details' => $e->getMessage() // TODO remove in production
            ], 500);
        }
    }

    /**
     * Supprime un profil existant.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroyProfile($id): JsonResponse
    {
        try {
            $metierProfile = new MetierProfile($id);

            $metierProfile->deleteProfile();

            return response()->json([
                'message' => 'Profil supprimé avec succès.',
                'status' => 200
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Profil non trouvé.',
                'status' => 404
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression du profil.',
                'status' => 500
            ], 500);
        }
    }
}
