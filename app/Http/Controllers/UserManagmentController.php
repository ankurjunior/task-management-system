<?php

namespace App\Http\Controllers;

use App\Models\MasterDistrict;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role_id !== 1) {
                abort(403, 'Unauthorized access to user management.');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'username',
            'name',
            'email',
            'mobile',
            'employee_code',
            'role_id',
            'designation_id',
            'district_id',
            'reporting_to_user_id',
            'is_active',
            'created_from',
            'created_to',
            'last_login_from',
            'last_login_to',
            'per_page',
        ]);

        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = in_array($perPage, [50, 100], true) ? $perPage : 50;

        $users = User::query()
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('master_designations', 'master_designations.id', '=', 'users.designation_id')
            ->leftJoin('master_districts', 'master_districts.id', '=', 'users.district_id')
            ->leftJoin('users as reporting_users', 'reporting_users.id', '=', 'users.reporting_to_user_id')
            ->select([
                'users.*',
                'roles.name as role_name',
                'master_designations.name as designation_name',
                'master_designations.hierarchy_level',
                'master_districts.division_name',
                'master_districts.district_name',
                'reporting_users.name as reporting_user_name',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('users.username', 'like', "%{$search}%")
                        ->orWhere('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('users.mobile', 'like', "%{$search}%")
                        ->orWhere('users.employee_code', 'like', "%{$search}%");
                });
            })
            ->when($filters['designation_id'] ?? null, fn ($query, $designationId) => $query->where('users.designation_id', $designationId))
            ->when($filters['district_id'] ?? null, fn ($query, $districtId) => $query->where('users.district_id', $districtId))
            ->when($filters['reporting_to_user_id'] ?? null, fn ($query, $reportingUserId) => $query->where('users.reporting_to_user_id', $reportingUserId))
            ->when($request->filled('is_active'), fn ($query) => $query->where('users.is_active', $request->boolean('is_active')))
            ->latest('users.created_at')
            ->paginate($perPage)
            ->withQueryString();

        $filterData = [
            'roles' => Role::orderBy('name')->get(),
            'designations' => DB::table('master_designations')->orderBy('hierarchy_level')->orderBy('name')->get(),
            'districts' => MasterDistrict::orderBy('division_name')->orderBy('district_name')->get(),
            'reportingUsers' => User::orderBy('name')->get(['id', 'name']),
            'perPageOptions' => [50, 100],
        ];

        return view('users.index', compact('users', 'filters', 'filterData'));
    }

    public function create()
    {
        return view('users.form', $this->formData(new User()));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['password'] = Hash::make($data['password']);
        $data['password_changed_at'] = null;

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.form', $this->formData($user));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validatedData($request, $user);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
            $data['password_changed_at'] = null;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $passwordRule = $user ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'];
        $employeeRole = Role::where('name', 'Employee')->where('is_active', true)->first();

        if (! $employeeRole) {
            abort(422, 'Employee role is not configured.');
        }

        $request->merge(['role_id' => $employeeRole->id]);

        $validator = Validator::make($request->all(), [
            'username'             => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user)],
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'mobile'               => ['nullable', 'string', 'max:20'],
            'employee_code'        => ['nullable', 'string', 'max:50', Rule::unique('users', 'employee_code')->ignore($user)],
            'password'             => $passwordRule,
            'role_id'              => ['required', 'exists:roles,id'],
            'designation_id'       => ['nullable', 'exists:master_designations,id'],
            'district_id'          => ['nullable', 'exists:master_districts,id'],
            'reporting_to_user_id' => ['nullable', 'exists:users,id'],
            'is_active'            => ['required', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($request, $user) {
            $designationId = $request->integer('designation_id');
            $reportingUserId = $request->integer('reporting_to_user_id');

            if (! $designationId) {
                return;
            }

            $designation = DB::table('master_designations')->find($designationId);
            $hierarchyLevel = $designation->hierarchy_level;

            if ($hierarchyLevel === 1 && $reportingUserId) {
                $validator->errors()->add('reporting_to_user_id', 'A level 1 user cannot report to another user.');
            } elseif ($hierarchyLevel > 1 && ! $reportingUserId) {
                $validator->errors()->add('reporting_to_user_id', 'Select the user this designation reports to.');
            } elseif ($reportingUserId) {
                if ($user && $reportingUserId === $user->id) {
                    $validator->errors()->add('reporting_to_user_id', 'A user cannot report to themselves.');

                    return;
                }

                $reportingUser = DB::table('users')
                    ->join('master_designations', 'master_designations.id', '=', 'users.designation_id')
                    ->where('users.id', $reportingUserId)
                    ->select('master_designations.hierarchy_level')
                    ->first();

                if (! $reportingUser || $reportingUser->hierarchy_level !== $hierarchyLevel - 1) {
                    $validator->errors()->add('reporting_to_user_id', 'Select a user from the immediately higher hierarchy level.');
                }
            }
        });

        return $validator->validate();
    }

    private function formData(User $user): array
    {
        $employeeRole = Role::where('name', 'Employee')->where('is_active', true)->firstOrFail();

        return [
            'user' => $user,
            'employeeRole' => $employeeRole,
            'designations' => DB::table('master_designations')->where('is_active', true)->orderBy('hierarchy_level')->orderBy('name')->get(),
            'districts' => MasterDistrict::orderBy('division_name')
                ->orderBy('district_name')
                ->get(),
            'reportingUsers' => DB::table('users')
                ->join('master_designations', 'master_designations.id', '=', 'users.designation_id')
                ->where('users.is_active', true)
                ->select('users.id', 'users.name', 'master_designations.name as designation_name', 'master_designations.hierarchy_level')
                ->orderBy('master_designations.hierarchy_level')
                ->orderBy('users.name')
                ->get(),
        ];
    }
}
